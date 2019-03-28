<?php

namespace App\Controllers\Api;



use App\Constants\BattleType;
use App\Constants\BlockID;
use App\Constants\FlowActionConst;
use App\Constants\LeaderBoardType;
use App\Constants\Message;
use App\Constants\MoneyType;
use App\Constants\PunishType;
use App\Controllers\CommonController;
use App\datalog\DigPigLog;
use App\datalog\GameEndFlow;
use App\datalog\GameStartFlow;
use App\Models\BattleToken;
use App\Models\DailyMissionLife;
use App\Models\Dao\LeaderboardDao;
use App\Models\Dao\UserInfoDao;
use App\Models\EliminatedDaily;
use App\Models\PunishDetail;
use App\Services\LeaderBoardService;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\App;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;


/**
 * 用户模块.
 * @Controller(prefix="/dailymission")
 */
class DailyMissionController extends CommonController
{
    const DAILYMISSION_PRICE = 50;

    const MONDAY_SETTLEMENT_DELAY = 18000;


    /**
     *
     * @RequestMapping(route="init")
     *（合并）每日任务初始化接口，返回用户剩余次数，购买次数的价格，时间和周赛排行榜编号等信息
     *
     * @return array
     */
    public function actionInit()
    {
        /**
         * 计算用户的剩余次数
         */
        $num = DailyMissionLife::getNumber();

        /**
         * 计算剩余时间
         */
        $nowTime = ServerTime::getTestTime();
        $today = strtotime(date('Y-m-d', $nowTime));
        $monday = Utils::monday();
        // 每周倒计时
        $residualTimeWeek = self::weekResidualTime($nowTime, $monday);
        // 每天24小时倒计时
        $residualTimeDay = 86400 - ($nowTime - $today);
        // 每周一4小时倒计时
        $residualTimeMonday = self::mondayResidualTime($nowTime, $monday);

        /**
         * 计算周赛的场次，格式2016-01-1，方便高分榜操作
         */
        $thisYear = date('o', $nowTime);

        /**
         * task all
         */
        $dailyTask = Config::loadJson('dailyTask');

        /**
         * 判断今天是否可以分享facebook
         */
        $giveNumber = DailyMissionLife::getGiveNumber();
        $isGive = ($giveNumber < 1);

        return $this->returnData([
            'residueNumber' => $num,
            'price' => static::DAILYMISSION_PRICE,
            'week' => $residualTimeWeek,
            'day' => $residualTimeDay,
            'monday' => $residualTimeMonday,
            'weekOfYear' => $thisYear . '-' . date("W", $nowTime),
            'serverTime' => time(),
            'dailyTask' => $dailyTask,
            'isGive' => $isGive
        ]);

    }

    /**
     * 玩一次dailymission（需要判断生命数量；需要判断是否在每周一 0~4 点中
     * @RequestMapping(route="start")
     */
    public function actionStart($log = [])
    {
        /**
         * 判断是否在周一前4个小时
         */
        $nowTime = ServerTime::getTestTime();
        $monday = Utils::monday();
        $residualTimeMonday = self::mondayResidualTime($nowTime, $monday);
        if ($residualTimeMonday > 0) {
            return $this->returnError(Message::WRONG_TIME_DAILYMISSION);
        }

        $residueNumber = DailyMissionLife::getNumber();
        /**
         * 判断是否还可以玩 (判断生命)
         */
        if ($residueNumber <= 0) {
            return $this->returnError(Message::LIFE_NOT_ENOUGH_DAILYMISSION);
        } else {
            /**
             * 更新数据
             */
            DailyMissionLife::incrBy('used');

            /**
             * 生产dailyMissionToken
             */
            $battleToken = new BattleToken();
            $battleToken->uid = globalInfo()->getUid();
            $battleToken->battleType = BattleType::DAILY_MISSION;
            $battleToken->secret = App::getBean('yiiSecurity')->generateRandomString(32);
            $battleToken->save();

            //对局开始log
            $gameStartFlow = GameStartFlow::newFlow(BattleType::DAILY_MISSION);
            $gameStartFlow->mergeClientLog($log);
            $gameStartFlow->send();

            return $this->returnData(['totalNumber' => ( int )$residueNumber - 1,
                'dailymissionToken' => $battleToken->secret]);

        }
    }

    /**
     * @RequestMapping(route="levelupdate")
     * @param array  $items 使用道具map
     * @param string $eliminatedPigToken 消猪数加密值
     * @param array  $log 需要记录的日志
     * @param $eliminated 消除元素map
     * @return array
     */
    public function actionLevelUpdate(array $items = [],string $eliminatedPigToken,array $log = [],$eliminated = [])
    {

        $globalInfo = globalInfo();

        //安全校验token，防止随意更改猪的数量
        $mineToken = BattleToken::findOne(['uid' => $globalInfo->getUid(), 'battleType' => BattleType::DAILY_MISSION]);

        if (empty($mineToken)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $securityTmp = App::getBean('security');
        $security = $securityTmp->withSecret($mineToken->secret);
        $eliminatedPigNum = $security->unHash($eliminatedPigToken);

        if (empty($eliminatedPigNum)) {//乱写token解密结果是空
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $eliminatedPigNum =(int)$eliminatedPigNum;//解密出来字符串转成数字

        //校验通过，删除token，防止重复请求
        $mineToken->delete();

        /**
         * 获取零收益信息
         */
        $zeroIncomeFlag = false;
        $endDate = '';
        $reason = '';
        if ($punishObj = PunishDetail::findOne(['openId' => $globalInfo->getThirdId(), 'type' => PunishType::ZERO_INCOME])) {
            $zeroIncomeFlag = true;
            $endDate = date("Y-m-d H:i:s",$punishObj->end_at);
            $reason = $punishObj->reason;

        }

        if (!empty ($items)) {
            foreach ($items as $itemId => $number) {
                if ($number > 0) {
                    bean(PropertyService::class)->handleOne($itemId, -$number, FlowActionConst::ACTION_ITME_DAILY_MISSION,'');
                }
            }
        }

        if (isset($eliminatedPigNum)) {
            $log['Item_pig'] = $eliminatedPigNum;
            //每日消猪目标数
            EliminatedDaily::incrItem(BlockID::PIG_NORMAL, $eliminatedPigNum);

            $punishDetail = PunishDetail::findOne(["openId" => $globalInfo->getThirdId(), "type" => PunishType::BAN_RANKING_DAILY_MISSION]);
            if(empty($punishDetail)){//没有禁止排行榜
                //更新每周和每日排行榜
                bean(LeaderBoardService::class)->updateByType($globalInfo->getUid(),LeaderBoardType::DAILY, $eliminatedPigNum);
                bean(LeaderBoardService::class)->updateByType($globalInfo->getUid(),LeaderBoardType::WEEKLY, $eliminatedPigNum);
            }

        }


        if (isset($log['giveUp'])) {
            /**
             * 新版本根据giveUp参数判断
             */
            if (!$log['giveUp']) {
                $rewardConf = Config::loadJson('dailyConfig');
                $silverReward = $rewardConf['dailyWinReward']['silver'];
                $goldReward = $rewardConf['dailyWinReward']['gold'];

                if ($zeroIncomeFlag){//判断零收益
                    $silverReward = 0;
                    $goldReward = 0;
                }

                if ($silverReward != 0) {
                    bean(PropertyService::class)->handleOne(MoneyType::SILVER, $silverReward, FlowActionConst::DETAIL_DAILY_TASK_PASS_REWARD,time());
                }

                if ($goldReward != 0) {
                    bean(PropertyService::class)->handleOne(MoneyType::GOLD, $goldReward, FlowActionConst::DETAIL_DAILY_TASK_PASS_REWARD,time());
                }
            }
        } else {
            /**
             * 旧版本每日任务模式有结算就赠送1000银币，无结算不赠送
             */
            $silverReward = 1000;
            if ($zeroIncomeFlag){//判断零收益
                $silverReward = 0;
            }
            bean(PropertyService::class)->handleOne(MoneyType::SILVER, $silverReward, FlowActionConst::DETAIL_DAILY_TASK_PASS_REWARD,time());
        }
        /**
         * 生成digpigLog
         */

        $digPigLog = new DigPigLog();
        $digPigLog->mergeClientLog($log);
        $digPigLog->send();


        //对局结束流水
        $itemsBuy = $log['itemBuy'] ?? [];
        $gameStartFlow =  GameEndFlow::newFlow($items, $itemsBuy, $eliminated);
        $gameStartFlow->RoundType = BattleType::DAILY_MISSION;
        $gameStartFlow->mergeClientLog($log);
        $gameStartFlow->send();

        return $this->returnData(['zeroIncomeInfo' =>
            ['zeroIncomeFlag'=>$zeroIncomeFlag,'reason'=>$reason,'endDate'=>$endDate]
        ]);
    }

    /**
     * 购买每日任务次数
     * @RequestMapping(route="buy")
     */
    public function actionBuy()
    {
        $globalInfo =globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        if ($userInfo->getGoldCoin() < self::DAILYMISSION_PRICE) {
            return $this->returnError(Message::MONEY_NOT_ENOUGH_DAILYMISSION);

        }

        $date = date('Y-m-d', ServerTime::getTestTime());
        bean(PropertyService::class)->handleOne(MoneyType::GOLD, -self::DAILYMISSION_PRICE, FlowActionConst::DETAIL_DAILY_MISSION_COST,$date);
        $purchaseNumber = DailyMissionLife::incrBy('purchase');

        return $this->returnData(['purchase_num' => $purchaseNumber]);

    }

    /**
     * 每周任务周一结算时间倒计时
     *
     * @param timeStamp $nowTime 当前时间戳
     * @param timeStamp $monday 本周一时间戳
     * @return number $residualTimeMonday > 0 结算剩余时间, 0 未到结算时间
     */
    private function mondayResidualTime($nowTime, $monday)
    {
        $diff = $nowTime - ($monday + self::MONDAY_SETTLEMENT_DELAY);
        $residualTimeMonday = 0;
        if (date('w', $nowTime) == 1 && $diff >= 0) {
            // 如果diff大于0 说明名已进入结算期
            $residualTimeMonday = 3600 - $diff;
        }
        if ($residualTimeMonday < 0) {
            $residualTimeMonday = 0;
        }
        return $residualTimeMonday;
    }

    /**
     * 每周任务剩余时间倒计时
     *
     * @param timeStamp $nowTime 当前时间戳
     * @param timeStamp $monday 本周一时间戳
     * @return number $residualTimeWeek
     */
    private function weekResidualTime($nowTime, $monday)
    {
        $diff = $nowTime - ($monday + self::MONDAY_SETTLEMENT_DELAY);
        // 每周倒计时
        if (date('w', $nowTime) == 1 && $diff < 0) {
            // 如果diff小于零，表示当前时间是周一结算时间之前，倒计时应以上周一为标准
            $residualTimeWeek = 86400 * 7 - ($nowTime - ($monday - 86400 * 7 + self::MONDAY_SETTLEMENT_DELAY));
        } else {
            $residualTimeWeek = 86400 * 7 - $diff;
        }
        return $residualTimeWeek;
    }
}