<?php

namespace App\Controllers\Api;

use App\Constants\BattleType;
use App\Constants\FlowActionConst;
use App\Constants\Message;
use App\Constants\MoneyType;
use App\Constants\PunishType;
use App\Constants\RedisKey;
use App\Controllers\CommonController;
use App\datalog\GameEndFlow;
use App\datalog\GameStartFlow;
use App\datalog\LostLevelLog;
use App\datalog\ScoreLog;
use App\Models\Dao\ItemDao;
use App\Models\Dao\MyFriendsDao;
use App\Models\Dao\ScoreDao;
use App\Models\Entity\MyFriends;
use App\Models\ProductBuyFlag;
use App\Models\PromotionConfig;
use App\Models\PunishDetail;
use App\Models\Dao\UserInfoDao;
use App\Services\PropertyService;
use App\Services\ReportDataService;
use App\Utils\Config;
use Swoft\App;
use Swoft\Auth\Mapping\AuthManagerInterface;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use yii\helpers\BaseArrayHelper;

/**
 * 用户模块.
 * @Controller(prefix="/score")
 */
class ScoreController extends CommonController
{
    const SILVER_REWARD = 50;

    /**
     * @RequestMapping(route="start")
     * 对局开始log
     * @param array $log
     * @return array
     */
    public function actionStart(array $log){
        $gameStartFlow = GameStartFlow::newFlow(BattleType::MAIN_TASK);
        $gameStartFlow->mergeClientLog($log);
        $gameStartFlow->send();
        return $this->returnSuccess();
    }


    /**
     * @RequestMapping(route="levelupdate")
     * 关卡数据更新接口
     * @param $win，是否胜利，0正常失败，1正常成功，2中途退出不重玩，3中途退出重新玩，4中途强退游戏，5客户端与服务器数据不一致，同步至服务器的数据
     * @param $level，关卡
     * @param $score，得分
     * @param $star，得星
     * @param $items，使用的道具
     * @param $eliminated，消除的元素
     * @param array $log，日志信息
     * @return array
     * @throws \Exception
     */
    public function actionLevelUpdate($win, $levelEncrypted, $scoreEncrypted, $starEncrypted, array $items= [],array $eliminated = [], $log = [])
    {

        $globalInfo = globalInfo();

        $secretKey = bean(AuthManagerInterface::class)->getSession()->getExtendedData()['secretKey'];
        $securityTmp = App::getBean('security');
        $security = $securityTmp->withSecret($secretKey);
        //解密level
        $level = $security->unHash($levelEncrypted);
        if (empty($level)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $level =(int)$level;

        //解密score
        $score = $security->unHash($scoreEncrypted);
        if (empty($score)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $score =(int)$score;//解密出来字符串转成数字

        //解密star
        $star = $security->unHash($starEncrypted);
        if (empty($star)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $star =(int)$star;//解密出来字符串转成数字

        /**
         * 获取零收益
         */
        $zeroIncomeFlag = false;
        $endDate = '';
        $reason = '';
        if ($punishObj = PunishDetail::findOne(['openId' => $globalInfo->getThirdId(), 'type' => PunishType::ZERO_INCOME])) {
            $zeroIncomeFlag = true;
            $endDate = date("Y-m-d H:i:s",$punishObj->end_at);
            $reason = $punishObj->reason;

        }
        /**
         * 获取基础信息
         */
        $uid = $globalInfo->getUid();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($uid);
        bean(UserInfoDao::class)->setDailyActive();

        /**
         * level参数控制
         */
        if ($level != $userInfo->getNewLevel()) {
            return $this->returnError(Message::LEVEL_REQUEST_INVALID);
        }

        $win = ( int )$win;
        $isFirstPass = false;
        /**
         * 成功参数设定：1,5成功，0,2,3,4失败
         */
        $isWin = ($win === 1 || $win === 5);

        $scoreLog = new ScoreLog();
        $scoreLog->mergeClientLog($log);

        $scoreLog->level = $level;
        $scoreLog->win = $win;
        $scoreLog->score = $score;
        $scoreLog->star = $star;
        $scoreLog->total_star = $userInfo->getStars();
        $scoreLog->items_count = array_sum($items);
        $scoreLog->silver = $userInfo->getSilverCoin();
        $scoreLog->gold = $userInfo->getGoldCoin();
        $scoreLog->lives = $userInfo->getLives();
        $scoreLog->isFirstPass = 0;

        /**
         * 失败扣除生命，成功记录关卡分数等信息
         */
        if (!$isWin) {
            $levelUp = null;
            bean(PropertyService::class)->handleOne(MoneyType::LIVE, -5,FlowActionConst::MAIN_LEVEL_FAIL_USE_LIVE,"");
        } else {
            $levelUp = ScoreDao::findOneByCondition([
                'uid' => $uid,
                'level' => $level
            ]);
            /**
             * 最高关卡加1
             */
            if ($level == $userInfo->getNewLevel()) {
                $userInfo->setNewLevel($level+1);
            } elseif ($level > $userInfo->getNewLevel()) {
                // 允许跳关打
                $userInfo->setNewLevel($level - $userInfo->getNewLevel() +1);

            }
            if ($levelUp === null) {
                /**
                 * 关卡信息不存在，说明没打过该关，新建记录
                 */
                $scoreLog->isFirstPass = 1;
                ScoreDao::createOneRecord($uid,$level,$score,$star);
                bean(PropertyService::class)->handleOne(MoneyType::REMAIN_STARS,1,FlowActionConst::DETAIL_FIRST_PASS_LEVEL,"");
                $userInfo->setStars($userInfo->getStars()+$star);

                $scoreLog->total_star += $star;

                $levelReward = isset($log['levelReward']) ? $log['levelReward'] : 0;
                $silverReward = $levelReward;

                //上报数据
                ReportDataService::ReportLevel($level,$globalInfo->getThirdId());
                /**
                 * 特权判定
                 */
                $platformRewardCfg = Config::loadJson('platformRewardCfg');
                $extraSilver = 0;
                if (UserInfoDao::isPlatformVip()) {//平台登陆特权
                    $extraSilverRate = $platformRewardCfg['qq_start']['extraSilverRate'];//目前qq微信奖励相同，全使用qq
                    $extraSilverTmp = max(1, floor($silverReward * $extraSilverRate));
                    $extraSilver += $extraSilverTmp;
                }
                if ($userInfo->getTencentVip() == 1) {//vip特权
                    $extraSilverRate = $platformRewardCfg['qq_vip']['extraSilverRate'];//
                    $extraSilverTmp = max(1, floor($silverReward * $extraSilverRate));
                    $extraSilver += $extraSilverTmp;
                }
                if ($userInfo->getTencentVip() == 2) {//svip特权
                    $extraSilverRate = $platformRewardCfg['qq_svip']['extraSilverRate'];//
                    $extraSilverTmp = max(1, floor($silverReward * $extraSilverRate));
                    $extraSilver += $extraSilverTmp;
                }
                //根据是否被处罚零收益判断银币
                $silverCount = $zeroIncomeFlag ? 0 : $silverReward + $extraSilver;
                bean(PropertyService::class)->handleOne(MoneyType::SILVER,$silverCount,FlowActionConst::DETAIL_FIRST_PASS_LEVEL,$level);


                /**
                 * 第一次打第N关加好友
                 */
                $levelArr = [
                    2,
                    4,
                    6,
                    8,
                    10
                ];
                if (in_array($level, $levelArr)) {
                    $this->addNewFriend($level);
                }
                $isFirstPass = true;


                /**
                 * 特定关卡解锁促销礼包
                 */
                $payPromotion = Config::loadJson('payPromotion');
                $unlockLevel = $payPromotion['unlockLevel'];
                if(in_array($level, $unlockLevel)){
                    $promotionConfig = new PromotionConfig();
                    $promotionConfig->uid = $globalInfo->getUid();

                    $buyFlag = ProductBuyFlag::findOne(['uid' => $uid, 'productId' => $payPromotion['list'][0]['id']]);
                    if($buyFlag){
                        $productId = $payPromotion['list'][1]['id'];
                    }else{
                        $productId = $payPromotion['list'][0]['id'];
                    }
                    $promotionConfig->productId = $productId;
                    $promotionConfig->expire = $payPromotion['time'];
                    $promotionConfig->save();
                }


            } else {
                /**
                 * 已打过的关卡，重新打，如果新数据分数更高就更新对应数据
                 */
//                if ($score > $levelUp->score) {
//                    $levelUp->score = $score;
//                }
//                $oldStar = $levelUp->star;
//                if ($star > $oldStar) {
//                    $levelUp->star = $star;
//                    $globalInfo->incr($star - $oldStar, 'star');
//                    $scoreLog->total_star += ($star - $oldStar);
//                    $silverReward = self::SILVER_REWARD;
//                    $globalInfo->incr($silverReward, 'silver', FlowActionConst::DETAIL_STAR_UPGRADE, $level);
//                } else {
//                    $offset = intval(count($levelData) / 2);
//                    if ($star <= $offset) {
//                        $silverReward = self::SILVER_REWARD;
//                        $globalInfo->incr($silverReward, 'silver', FlowActionConst::DETAIL_PASS_LEVEL, $level);
//                    }
//                }
//                $levelUp->update();
            }
        }
        /**
         * scoreLog
         */
        $scoreLog->topLevel = $userInfo->getNewLevel();
        $scoreLog->send();





        if (empty($levelUp)) {
            $ret = [];
        } else {
            $ret = $levelUp->toArray();
        }

        /**
         * 更新道具使用信息
         */
        if (!empty ($items)) {
            //用户当前道具数
            $itemIds = [];
            $find = bean(ItemDao::class)->findItemsByUid($globalInfo->getUid());
            foreach ($find as $value) {
                $itemIds[$value['item']] = $value['number'];
            }
            foreach ($items as $itemId => $number) {
                if ($number > 0) {
                    bean(PropertyService::class)->handleOne($itemId,-$number,FlowActionConst::ACTION_ITME_LEVEL_PASS_SPEND,$level);

                }


            }
        }

        /**
         * 记录消除元素的信息
         */
        if (!empty ($eliminated) && $level <= $userInfo->getNewLevel() && $isWin) {
            /**
             * 重新写一个更新的方法
             */
            //Eliminated::updateNumberByType($uid, $eliminated);
        }

        $itemsBuy = $log['itemBuy'] ?? [];

        $gameStartFlow =  GameEndFlow::newFlow($items, $itemsBuy, $eliminated);
        $gameStartFlow->RoundType = BattleType::MAIN_TASK;
        $gameStartFlow->mergeClientLog($log);
        $gameStartFlow->RoundIncomeGold = $silverCount ?? 0;
        $gameStartFlow->RoundLives = $userInfo->getLives();
        $gameStartFlow->send();


        /**
         * 最后保存userInfo
         */
        bean(UserInfoDao::class)->updateUserInfo($userInfo);
        /**
         * 返回
         */
        return [
            'code' => 1,
            'score' => $ret,
            'isFirstPass' => $isFirstPass,
            'zeroIncomeInfo' => ['zeroIncomeFlag'=>$zeroIncomeFlag,'reason'=>$reason,'endDate'=>$endDate]
        ];
    }

    /**
     * @RequestMapping(route="getscores")
     * 获取用户所有关卡信息
     * @return array
     */
    public function actionGetScores()
    {
        $globalInfo = globalInfo();
        $all = ScoreDao::findAllByUid([
            'uid' => $globalInfo->getUid()
        ]);

        $number = count($all);
        if ($number) {
            $topLevel = $all[$number - 1]['level'];
            if ($topLevel != $number) {
                /**
                 * 数据有缺失，记录到日志中
                 */
                $levels = BaseArrayHelper::getColumn($all, 'level', false);
                $lostLevel = [];
                for ($i = 1; $i < $topLevel; $i++) {
                    if (array_search($i, $levels) === false) {
                        $lostLevel[] = $i;
                    }
                }
                $lostLevelLog = new LostLevelLog();
                $lostLevelLog->lostLevel = $lostLevel;
                $lostLevelLog->send();
            }
        }
        return [
            "code" => 1,
            "scores" => $all
        ];
    }

    /**
     * 根据等级添加随机好友，和之前的一名玩家互加好友，下一个玩家也会跟这个玩家互加好友
     */
    protected function addNewFriend($level)
    {
        // 获取用户id
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();

        // 取数据
        /**
         * redisMaster最好不分服，全球通用，否则每个服需要维护一套对应等级的好友列表
         */
        $redis =bean('redis');
        /**
         * redis不可用时直接返回，不分配好友
         */
        if ($redis === false) {
            return;
        }

        $key = $this->getNewFriendsListKey($level);
        $newFriendList = $redis->lrange($key, 0, -1);

        /**
         * 好友去重
         */

        $friendsSet = MyFriendsDao::findAllFriendsUid($uid);
        $diff = array_diff($newFriendList, $friendsSet);

        /**
         * 获取去重最后一个入栈的用户，互加好友
         */
        $newFriendUid = array_pop($diff);
        if (!is_null($newFriendUid) && $newFriendUid != $uid) {

            $myFriends = new MyFriends();
            $myFriends->setUid($uid);
            $myFriends->setFuid($newFriendUid);
            $myFriends->setCreatedAt(time());
            $myFriends->setUpdatedAt(time());
            $myFriends->save()->getResult();

            $myFriends = new MyFriends();
            $myFriends->setUid($newFriendUid);
            $myFriends->setFuid($uid);
            $myFriends->setCreatedAt(time());
            $myFriends->setUpdatedAt(time());
            $myFriends->save()->getResult();

        }

        /**
         * 维持长度为20的队列，如果大于等于20，从头部开始删除
         */
        $friendNum = $redis->llen($key);
        if ($friendNum >= 20) {
            $redis->lpop($key);
        }

        /**
         * 用户入栈尾
         */
        $redis->rpush($key, $uid);
    }

    private function getNewFriendsListKey($level)
    {
        return sprintf(RedisKey::GET_NEW_FRIENDS, $level);
    }
}