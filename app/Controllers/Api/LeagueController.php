<?php

namespace App\Controllers\Api;

use App\Constants\BattleType;
use App\Constants\FlowActionConst;
use App\Constants\ItemID;
use App\Constants\LeagueRankType;
use App\Constants\Message;
use App\Constants\MoneyType;
use App\Constants\PunishType;
use App\Controllers\CommonController;
use App\datalog\GameEndFlow;
use App\datalog\GameStartFlow;
use App\datalog\LeagueChestReward;
use App\datalog\MTLeagueLogin;
use App\datalog\MTLeagueScore;
use App\Models\BattleToken;
use App\Models\Dao\CenterUserDao;
use App\Models\PunishDetail;
use App\Models\Dao\UserInfoDao;
use App\Services\LeagueRankingService;
use App\Services\LeagueRewardService;
use App\Services\LeagueService;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Utils\Utils;
use Swoft\App;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use yii\helpers\ArrayHelper;

/**
 * 用户模块.
 * @Controller(prefix="/league")
 */
class LeagueController extends CommonController
{


    /**
     * 用户初始化
     * @RequestMapping(route="init")
     * @return array
     */
    public function actionInit()
    {
        $globalInfo = globalInfo();
        $leagueRankingService = bean(LeagueRankingService::class);
        $leagueService = bean(LeagueService::class);
        $lResidualTime = $leagueService->startAndEndTime();
        $currentOrNextSeason = $leagueService->currentOrNextSeason();
        $lastSeason = $leagueService->getPreSeason();
        $lastSeasonName = isset($lastSeason['name']) ? $lastSeason['name'] : '';
        $lRewardTag = $leagueService->rankRewardIsReceived($globalInfo->getUuid());
        $lUserAchieve = [];
        if (!$lRewardTag) {
            $rewardConfig = Config::loadJson('leagueReward');
            $rankDanInfo = $leagueService->getRank($globalInfo);
            $isSettle = $leagueService->isSettlement();
            if (!$isSettle) {
                $rankDan = $rankDanInfo['rank'] - $rankDanInfo['upDown'];
                if ($rankDan < 0) {
                    $rankDan = 0;
                }
                if ($rankDan > 6) {
                    $rankDan = 6;
                }
            } else {
                $rankDan = $rankDanInfo['rank'];
            }
            $lPreRank = $leagueRankingService->getPrevUserRank($globalInfo->getUuid(), $rankDan);
            $name = $leagueService->getNameNumMap($rankDanInfo['rank']);
            $rewardIndex = array_keys($rewardConfig[$name]);
            rsort($rewardIndex);
            if ($rewardIndex[0] >= $lPreRank['rank']) {
                $lUserAchieve = [
                    'rank' => !isset($lPreRank['rank']) ? false : ($lPreRank['rank'] + 1),
                    'rankDan' => $rankDan
                ];
            }
        }

        return [
            'code' => 1,
            'residualTime' => $lResidualTime,
            'userAchieve' => $lUserAchieve,
            'lastSeason' => $lastSeasonName,
            'season' => isset($currentOrNextSeason) ? $currentOrNextSeason : '',
        ];

    }

    /**
     * 点击联赛按钮调用
     * @RequestMapping(route="getinfo")
     */
    public function actionGetInfo()
    {

        /**
         * 获取基础信息
         */
        $globalInfo = globalInfo();
        $leagueRankingService = bean(LeagueRankingService::class);
        $leagueService = bean(LeagueService::class);

        $leagueRewardService = bean(LeagueRewardService::class);

        /**
         * @todo 获得用户当前段位
         */
        $rankDan = $leagueService->getRank($globalInfo);
        // 获取本赛季最高关卡与总分数
        $leagueService->initialize($globalInfo->getUuid());
        $levelInfo = $leagueService->getLevelInfo($globalInfo->getUuid(), true);
        $residualTime = $leagueService->startAndEndTime(); //计算当前赛季的结束倒计时，若在结算期，计算下个赛季的开始时间


        /**
         * 检查金色关卡
         */
        $goldenLevel = $leagueRankingService->checkGoldenLevel($globalInfo->getUuid());
        //检查关卡礼盒
        $rewardedLevelBox = [];
        $levelBoxRewarded = $leagueRewardService->getLevelBoxRewardList($globalInfo->getUuid());
        if (!empty($levelBoxRewarded)) {
            $rewardedLevelBox = ArrayHelper::getColumn($levelBoxRewarded, 'level');
        }
        $season = $leagueService->getSeason();

        $logData['leagueSeason'] = (int)str_replace('.', '', isset($season['name']) ? $season['name'] : "");
        $logData['rankDan'] = isset($rankDan['rank']) ? $rankDan['rank'] : 0;


       $mTLeagueLogin = new MTLeagueLogin();
       $mTLeagueLogin->mergeClientLog($logData);
       $mTLeagueLogin->send();


        return [
            'code' => 1,
            'levelInfo' => isset($levelInfo['levelInfo']) ? $levelInfo['levelInfo'] : [],
            'totalScores' => isset($levelInfo['totalScores']) ? $levelInfo['totalScores'] : 0,
            'rankDan' => $rankDan,
            'residualTime' => $residualTime,
            'goldenLevel' => $goldenLevel,
            'rewardedLevelBox' => $rewardedLevelBox
        ];
    }

    /**
     * 联赛对局开始
     * @RequestMapping(route="start")
     * @param array $log
     * @return array
     */
    public function actionStart($log = [])
    {
        $gameStartFlow = GameStartFlow::newFlow(BattleType::LEAGUE);
        $gameStartFlow->mergeClientLog($log);
        $gameStartFlow->send();

        $battleToken = new BattleToken();
        $battleToken->uid = globalInfo()->getUid();
        $battleToken->battleType = BattleType::LEAGUE;
        $battleToken->secret = App::getBean('yiiSecurity')->generateRandomString(32);
        $battleToken->save();

        return $this->returnData(['battleToken' => $battleToken->secret]);

    }

    /**
     * 联赛对局结算
     * @RequestMapping(route="levelupdate")
     * @param       $win
     * @param       $levelToken
     * @param       $scoreToken
     * @param       $star
     * @param array $items
     * @param array $eliminated
     * @param array $log
     * @param bool $rankCanBeTag
     * @param null $clientSeason
     *
     * @return array
     */
    public function actionLevelUpdate($win, $levelToken, $scoreToken, $star, array $items = [], array $eliminated = [], array $log = [], $rankCanBeTag = false, $clientSeason = null)
    {
        $leagueService = bean(LeagueService::class);
        $leagueRankingService = bean(LeagueRankingService::class);
        $leagueRewardService = bean(LeagueRewardService::class);
        if ($win != 4) {
            if ($clientSeason !== null) {
                $judge = $leagueService->seasonEqual($clientSeason);
                if (!$judge) {
                    return [
                        'code' => 34010,
                        'message' => 'season name unequal, please get again!'
                    ];
                }
            } else {
                if ($leagueService->isSettlement()) {
                    return [
                        'code' => 34010,
                        'message' => 'season name unequal, please get again!'
                    ];
                }
            }
        }

        /**
         * 获取基础信息
         */
        $globalInfo = globalInfo();

        //安全校验token，防止随意更改猪的数量
        $battleToken = BattleToken::findOne(['uid' => $globalInfo->getUid(), 'battleType' => BattleType::LEAGUE]);
        if (empty($battleToken)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }

        $securityTmp = App::getBean('security');
        $security = $securityTmp->withSecret($battleToken->secret);
        //解密level

        $level = $security->unHash($levelToken);
        if (empty($level)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $level = (int)$level;

        //解密score
        $score = $security->unHash($scoreToken);
        if (empty($score)) {
            return $this->returnError(Message::WRONG_DAILYMISSION_TOKEN);
        }
        $score = (int)$score;//解密出来字符串转成数字

        //校验通过，删除token，防止重复请求
        $battleToken->delete();
        /**
         * 获取零收益
         */
        $zeroIncomeFlag = false;
        $remainTime = '';
        $reason = '';

        if ($punishObj = PunishDetail::findOne(['openId' => $globalInfo->getThirdId(), 'type' => PunishType::ZERO_INCOME])) {
            $zeroIncomeFlag = true;
            $remainTime = $punishObj->getTimeToLive();
            $reason = $punishObj->reason;

        }


        $uuid = $globalInfo->getUuid();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        $actTime = time();
        $clientTime = $actTime;
        /**
         * 日志信息
         */
        $logData = [
            "type" => "business",
            "uuid" => $globalInfo->getUuid(),
            "customerId" => ( int )$globalInfo->getCustomerId(),
            "channel" => $globalInfo->getChannel(),
            "level" => $level,
            "win" => $win,
            "score" => $score,
            "star" => $star,
            "map_version" => isset ($log ['map_version']) ? $log ['map_version'] : 0,
            "map_name" => isset ($log ['map_name']) ? $log ['map_name'] : '',
            "items_count" => empty ($items) ? 0 : array_sum($items),
            "step" => isset ($log ['step']) ? $log ['step'] : 0,
            "duration" => isset ($log ['duration']) ? $log ['duration'] : 0,
            "counts" => isset ($log ['counts']) ? $log ['counts'] : 0,
            "actTime" => $actTime,
            "clientTime" => $clientTime,
            "silver" => $userInfo->getSilverCoin(),
            "gold" => $userInfo->getGoldCoin(),
            "lives" => $userInfo->getLives(),
            "logId" => isset ($_GET ["logId"]) ? $_GET ["logId"] : "",
            "register_time" => ( int )$globalInfo->getCreatedAt(),
            "isFirstPass" => 0, // 是否是第一次过关，0不是，1是
            "difficult" => isset ($log ['difficult']) ? $log ['difficult'] : 0,
            "totalScore" => isset ($log ['totalScore']) ? $log ['totalScore'] : 0,
            "totalStar" => isset ($log ['totalStar']) ? $log ['totalStar'] : 0,
            "map_id" => isset($log ['map_id']) ? $log ['map_id'] : 0
        ];

        /**
         * 成功参数设定：1,5成功，0,2,3,4失败
         */
        $isWin = ($win === 1 || $win === 5);
        /**
         * 更新关卡信息
         */
        $isTopest = false;
        $leagueService->initialize($uuid);
        // 失败扣除生命
        if (!$isWin) {
            // 更新用的生命值
            bean(PropertyService::class)->handleOne(MoneyType::LIVE, -5, FlowActionConst::ACTION_ITME_LEVEL_PASS_REWARD);
            $userGroup = 'unknown';
            if ($level != 1) {
                $userGroup = $leagueRankingService->getGroupRankKey($globalInfo);
            }
        } else {
            // 获得更新该关卡信息前该用户的排名与总分数
            // 更新用户关卡信息(包括最高关卡),并更新排行榜
            /**
             * @todo 这部分重写
             */
            //更新当前关卡的排行榜
            $levelResult = $leagueService->incrLevelInfo($level, $star, $score);
            if ($levelResult !== null) {
                // 获得用户排名上升的幅度,与其前三后三用户
                $rankAfter = $leagueRankingService->getUserRank($uuid);
                // 判断用户是否达到排名宝箱，如果达到在redis中打标记
                if ($rankCanBeTag) {
                    $leagueRewardService->setTagOfRankChest($rankAfter ['rank'], $uuid);
                }
                $isTopest = true;
                // 更新单关排行榜
                $leagueRankingService->updateLevelRankingList($level, $score, $uuid);
                //判断当前关卡，是不是第一次玩
                if ($levelResult === $leagueService::FIRST_PASS_LEVEL) {
                    $logData ['isFirstPass'] = 1;
                }
            }
            // 获得单关排行榜前100名,并判断有没有在100名内
            $levelRank = $leagueRankingService->getTopNofLevelRank($level, $uuid);
            foreach ($levelRank as $key => $levelOne) {
                if ($levelOne['user'] == $uuid) {
                    $levelRank[$key]['name'] = $userInfo->getUsername();
                } else {
                    $levelRank[$key]['name'] = Utils::randomName();
                }
            }
            //获取用户单关的排名与分数
            $levelRankSelf = $leagueRankingService->getUserLevelRank($level, $uuid);
            $userGroup = $leagueRankingService->getGroupRankKey($globalInfo);
        }
        $topLevel = $leagueService->getNewLevel() - 1;
        $rankDan = $leagueService->getRank($globalInfo);
        /**
         * 日志数据处理
         */
        $logData['topLevel'] = $topLevel;
        $logData['leagueSeason'] = (int)str_replace('.', '', $clientSeason);
        $logData['rankDan'] = $rankDan['rank'];
        $logData['leagueUserGroup'] = $userGroup;
        $logData['clientRankDan'] = isset($log['clientRankDan']) ? (int)$log['clientRankDan'] : -1;
        $logData['clientIsRich'] = isset($log['clientIsRich']) ? (int)$log['clientIsRich'] : -1;
        $logData['clientGroupNumber'] = isset($log['clientGroupNumber']) ? (int)$log['clientGroupNumber'] : -1;
        $mTLeagueScore = new MTLeagueScore();
        $mTLeagueScore->mergeClientLog($logData);
        $mTLeagueScore->send();

        /**
         * 客户端不知道League信息,且不是打第一关失败（$userGroup是unknown）时返回给客户端League信息
         */
        $serverLeague = [];
        if ($logData['clientRankDan'] == -1 && $userGroup !== 'unknown') {
            // 统计分析拆开记录，方便使用，避免遍历数据
            $groupInfo = explode(":", $userGroup);
            if (is_array($groupInfo) && (count($groupInfo) > 3)) {
                if ($groupInfo[2] > LeagueRankType::Silver || !isset($groupInfo[3])) {
                    // 白银以上需修正isRich
                    $realGroupInfo = $leagueRankingService->getRealGroupInfo();
                    $serverLeague['leagueIsRich'] = isset($realGroupInfo['isRich']) ? (int)$realGroupInfo['isRich'] : -1;
                } else {
                    // 青铜白银$groupInfo包含
                    $serverLeague['leagueIsRich'] = (int)$groupInfo[3];
                }

                $groupInfoNumber = count($groupInfo);
                $serverLeague['leagueGroupNumber'] = -1;
                if ($groupInfoNumber >= 4) {
                    $serverLeague['leagueGroupNumber'] = (int)$groupInfo[$groupInfoNumber - 1];
                }

                $serverLeague['rankDan'] = (int)$groupInfo[2];
            }
        }

        /**
         * 更新道具使用信息
         */
        if (!empty ($items)) {
            foreach ($items as $itemId => $number) {
                if ($number > 0) {
                    bean(PropertyService::class)->handleOne($itemId, -$number, FlowActionConst::ACTION_ITME_LEVEL_PASS_SPEND,$level);
                }

            }
        }

        //对局结束流水
        $itemsBuy = $log['itemBuy'] ?? [];
        $gameEndFlow =  GameEndFlow::newFlow($items, $itemsBuy, $eliminated);
        $gameEndFlow->RoundType = BattleType::LEAGUE;
        $gameEndFlow->mergeClientLog($log);
        $gameEndFlow->send();

        return [
            'code' => 1,
            'topestScore' => $isTopest,
            'levelRank' => isset($levelRank) ? $levelRank : [],
            'levelRankSelf' => isset($levelRankSelf) ? $levelRankSelf : [],
            'rankDan' => isset($serverLeague['rankDan']) ? (int)$serverLeague['rankDan'] : -1,
            'isRich' => isset($serverLeague['leagueIsRich']) ? (int)$serverLeague['leagueIsRich'] : -1,
            'groupNumber' => isset($serverLeague['leagueGroupNumber']) ? (int)$serverLeague['leagueGroupNumber'] : -1
        ];
    }


    /**
     * 读取天梯的排行榜信息接口
     * @RequestMapping(route="rankinglist")
     */
    public function actionRankingList()
    {
        $globalInfo = globalInfo();

        if ($punishObj = PunishDetail::findOne(['openId' => $globalInfo->getThirdId(), 'type' => PunishType::BAN_RANKING_LEAGUE])) {//判断是否被禁止排行榜
            $end_at = $punishObj->end_at;
            $reason = $punishObj->reason;
            $dateTime = date("Y-m-d H:i:s", $end_at);
            return $this->returnError(Message::BAN_RANKING_STATUS, $reason . "|" . $dateTime);

        }
        $leagueRankingService = bean(LeagueRankingService::class);

        $globalRank = $leagueRankingService->getTopNOfRanking();

        $uuidArr = [];
        $groupRankKey = $leagueRankingService->getGroupRankKey($globalInfo);
        //redisKey分两种情况，青铜白银，有钱和没钱用户分开分组
        if (substr_count($groupRankKey, ":") == 4) {//青铜白银
            $keyArr = explode(":", $groupRankKey);// 0=>没用，1=>season,2=>rank,3=>isRich,4=>groupNumber
            $groupNumber = $keyArr[4];
        } else {//黄金及以上
            $keyArr = explode(":", $groupRankKey);//  0=>没用，1=>season,2=>rank,3=>groupNumber
            $groupNumber = $keyArr[3];
        }

        $botNameArr = $leagueRankingService->getBotName($groupNumber);
        //先找真人的uuid
        foreach ($globalRank as $key => $global) {
            if (!preg_match("/^bot-/", $global['user'])) {//找出真人的uuid
                $uuidArr[] = $global['user'];
            }
        }
        $uuidToUidArr = $uidToNameArr= $userInfoArr = [];

        if (!empty($uuidArr)){
            //  uuidArr转换成[uuid=>uid]
            $uuidToUidArr = CenterUserDao::uuidArrToUidArr($uuidArr);
            $uidArr = array_values($uuidToUidArr);
            // 得到真人的info
            $userInfoArr = bean(UserInfoDao::class)->getAllByUidArr($uidArr);
            $uidToNameArr = [];
            //生成[uid=>username,...]
            foreach ($userInfoArr as $value) {
                $uidToNameArr[$value['uid']] = $value['username'];
            }
        }


        foreach ($globalRank as $key => $global) {
            $globalRank [$key] ['bestMedal'] = 0;

            if (preg_match("/^bot-/", $global['user'])) {//机器人
                $globalRank [$key] ['name'] = $botNameArr[$global ['user']];
            } else {//真人
                $uidTmp = $uuidToUidArr[$global ['user']];
                $globalRank [$key] ['name'] = $uidToNameArr[$uidTmp];
            }
            //加入uid,机器人没有uid给0
            $globalRank[$key]['uid'] = isset($uuidToUidArr[$globalRank[$key]['user']]) ? (string)$uuidToUidArr[$globalRank[$key]['user']] : '0';
        }


        return [
            'code' => 1,
            'rankList' => $globalRank,
        ];
    }

    /**
     * 读取天梯的排行榜信息接口
     * @RequestMapping(route="prerankinglist")
     */
    public function actionPreRankingList()
    {
        $globalInfo = globalInfo();
        $uuid = $globalInfo->getUuid();
        $leagueRankingService = bean(LeagueRankingService::class);
        $leagueService = bean(LeagueService::class);
        $rankDanInfo = $leagueService->getRank($globalInfo);
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        if (!$leagueService->isSettlement()) {
            $rankDan = $rankDanInfo['rank'] - $rankDanInfo['upDown'];
            if ($rankDan < 0) {
                $rankDan = 0;
            }
            if ($rankDan > 6) {
                $rankDan = 6;
            }
        } else {
            $rankDan = $rankDanInfo['rank'];
        }
        $globalRank = $leagueRankingService->getPreRankList($rankDan);
        foreach ($globalRank as $key => $global) {
            $globalRank [$key] ['bestMedal'] = 0;
            if ($uuid == $global ['user']) {
                $globalRank [$key] ['name'] = $userInfo->getUsername();
            } else {
                $globalRank [$key] ['name'] = Utils::randomName();
            }
        }
        return [
            'code' => 1,
            'rankList' => $globalRank,
        ];
    }

    /**
     * 天梯的排名奖励领取，上个赛季发放的奖励
     * @RequestMapping(route="rankreward")
     */
    public function actionRankReward()
    {
        /**
         * 获取基础信息
         */
        $globalInfo = globalInfo();
        // 判断用户是否已经领取过上个赛季的排名奖励
        $leagueService = bean(LeagueService::class);
        $leagueRankingService = bean(LeagueRankingService::class);
        $tag = $leagueService->rankRewardIsReceived($globalInfo->getUuid());
        $reward = [];
        if ($tag) {
            // 如果已经领取过返回错误
            return [
                'code' => 34001,
                'message' => 'rank Award has been received!'
            ];
        }
        // 读取任务配置文件
        $task = Config::loadJson('piggyTowerSpecialTask')['rank'];
        // 读取用户上个赛季排名
        $rankDanInfo = $leagueService->getRank($globalInfo);
        //判断当前是不是在结算期
        $isSettle = $leagueService->isSettlement();
        if (!$isSettle) {
            $rankDan = $rankDanInfo['rank'] - $rankDanInfo['upDown'];
            if ($rankDan < 0) {
                $rankDan = 0;
            }
            if ($rankDan > 6) {
                $rankDan = 6;
            }

        } else {
            $rankDan = $rankDanInfo['rank'];
        }
        //获取上个赛季的排名与分数
        $preRank = $leagueRankingService->getPrevUserRank($globalInfo->getUuid(), $rankDan);
        if (!isset($preRank ['rank'])) {
            return [
                'code' => 34002,
                'message' => 'user\'s rank of last season not found!'
            ];
        }
        //获取段位
        $rankName = $leagueService->getNameNumMap($rankDan);
        $task = Config::loadJson('leagueReward')[$rankName];
        // 从配置中读取出用户应得的奖励内容
        $sections = array_keys($task);
        $rank = $preRank ['rank'] + 1;
        if ($rank <= 3) {
            $reward = $task [$rank];
        }
        $isSunShine = true;
        foreach ($sections as $section) {
            if ($rank <= $section) {
                $reward = $task [$section];
                $isSunShine = false;
                break;
            }
        }

        /**
         * 不在排名之内，发放阳光普照奖励
         */
        if (!$isSunShine) {
            // 发放相应奖励
            bean(PropertyService::class)->handleBatch($reward, FlowActionConst::DETAIL_MT_LEAGUE_RANK, $leagueService->getPreSeasonName());

        } else {
            $reward = [];
        }
        // 在redis中做已经领取过该奖励的tag
        $leagueService->setRankReward($globalInfo->getUuid());
        // 返回奖励细节
        return [
            'code' => 1,
            'rewardDetail' => $reward
        ];
    }

    /**
     * 天梯的宝箱奖励领取
     * param string $type  'normal','rank'任务类型
     * param integer $taskId   任务ID
     *
     * @RequestMapping(route="chestreward")
     */
    public function actionChestReward($type, $taskId, $clientSeason = null)
    {
        $leagueService = bean(LeagueService::class);
        $leagueRewardService = bean(LeagueRewardService::class);
        if ($clientSeason !== null) {
            $judge = $leagueService->seasonEqual($clientSeason);
            if (!$judge) {
                return [
                    'code' => 34010,
                    'message' => 'season name unequal, please get again!'
                ];
            }
        }
        /**
         * 获取基础信息
         */
        $globalInfo = globalInfo();
        if (!in_array($type, [
            'normal',
            'rank'
        ])
        ) {
            return [
                'code' => 34004,
                'message' => 'type not exist!'
            ];
        }
        // 读取任务配置文件
        $task = Config::loadJson('leagueTask')['chest'][$type];
        if (!array_key_exists($taskId, $task)) {
            return [
                'code' => 34004,
                'message' => 'this chest is not exist!'
            ];
        }
        if ($type == 'normal') {
            // 判断用户在当前赛季是否已领取过该奖励
            $list = $leagueRewardService->getPiggyTowerChestRewardList($globalInfo->getUuid());
            if (in_array($taskId, $list)) {
                return [
                    'code' => 34003,
                    'message' => 'this chest has been received!'
                ];
            }
            // 读取配置中的领取条件
            $tag = $task [$taskId] [0];
            // 判断用户是否完成该任务
            $completeBool = true;
            foreach ($tag as $key => $value) {
                $completeBool = ($leagueRewardService->judgeComplete($key, $value, $globalInfo->getUuid()) && $completeBool);
            }
            if (!$completeBool) {
                return [
                    'code' => 34005,
                    'message' => 'task not complete!'
                ];
            }
            // 标记已领取
            $complete = [
                'taskId' => $taskId,
                'rewardTime' => time()
            ];
            $leagueRewardService->setPiggyTowerChestRewardList($complete, $globalInfo->getUuid());
        }
        if ($type == 'rank') {
            // 判断用户是否是否达到目标，或者有没有领取过
            $redis = bean('redis');
            $key = $leagueRewardService->getRankChestTagKey($taskId, $globalInfo->getUuid());

            $tag = $redis->get($key);
            if (empty($tag)) {
                return [
                    'code' => 34005,
                    'message' => 'task not complete!'
                ];
            }
            if ($tag == 1) {
                return [
                    'code' => 34003,
                    'message' => 'this chest has been received!'
                ];
            }
            // 标记已领取
            $redis->setEx($key, $leagueRewardService::SEASON_INFO_VALID_TIME, 1);
        }
        // 发放相应的奖励列表
        $reward = $task [$taskId] [1];

        bean(PropertyService::class)->handleBatch($reward, FlowActionConst::DETAIL_MT_LEAGUE_RANK, $leagueService->currentOrNextSeason());

        $logData = [
            "rewardType" => $type,
            "taskId" => $taskId,
            "model" => 'leagueChestReward'

        ];

        $leagueChestReward = new LeagueChestReward();
        $leagueChestReward->mergeClientLog($logData);
        $leagueChestReward->send();
        // 返回奖励细节
        return [
            'code' => 1,
            'rewardDetail' => $reward
        ];
    }

    /**
     * 天梯的关卡宝箱奖励
     * @RequestMapping(route="levelboxreward")
     */
    public function actionLevelBoxReward($level, $clientSeason = null, $pieceId = null)
    {
        $leagueService = bean(LeagueService::class);
        $leagueRewardService = bean(LeagueRewardService::class);
        if ($clientSeason !== null) {
            $djuge = $leagueService->seasonEqual($clientSeason);
            if (!$djuge) {
                return [
                    'code' => 34010,
                    'message' => 'season name unequal, please get again!'
                ];
            }
        }
        $globalInfo = globalInfo();
        $task = Config::loadJson('leagueTask')['levelBox'];
        /**
         * 判断关卡奖励中是否有该奖励
         */
        $levels = array_keys($task);
        if (!in_array($level, $levels)) {
            return [
                'code' => 34006,
                'message' => 'this levelBox not exist!'
            ];
        }
        /**
         * 判断用户是否领取过该奖励
         */
        $rewardedLevels = [];
        $levelBoxRewarded = $leagueRewardService->getLevelBoxRewardList($globalInfo->getUuid());
        if (!empty($levelBoxRewarded)) {
            $rewardedLevels = ArrayHelper::getColumn($levelBoxRewarded, 'level');
        }
        if (in_array($level, $rewardedLevels)) {
            return [
                "code" => 34007,
                "message" => 'this levelBox has been rewarded!'
            ];
        }
        /**
         * 判断用户达到领奖要求
         */

        $leagueService->initialize($globalInfo->getUuid());
        $topLevel = $leagueService->getNewLevel() - 1;
        if ($topLevel < $level) {
            return [
                "code" => 34008,
                "message" => 'must pass the appointed level!'
            ];
        }
        /**
         * 发放相应奖励
         */
        $reward = $task [$level];
        bean(PropertyService::class)->handleBatch($reward, FlowActionConst::DETAIL_MT_LEAGUE_LEVEL, $leagueService->currentOrNextSeason());



        /**
         * 将该关卡加入已完成列表
         */
        $leagueRewardService->setLevelBoxRewardList($globalInfo->getUuid(), $level);
        /**
         * 返回奖励细节
         */
        $logData = [
            "rewardType" => 'level gift',
            "taskId" => $level,
            "model" => 'piggyTowerSpecialChestReward'
        ];
        //这里两个log字段类型相同，合并
        $leagueChestReward = new LeagueChestReward();
        $leagueChestReward->mergeClientLog($logData);
        $leagueChestReward->send();

        return [
            "code" => 1,
            "rewardDetail" => $reward
        ];
    }

    /**
     * 天梯的任务列表
     * @RequestMapping(route="tasklist")
     */
    public function actionTaskList()
    {
        $globalInfo = globalInfo();

        $leagueRewardService = bean(LeagueRewardService::class);

        // 查询用户已领取的普通宝箱奖励
        $normal = $leagueRewardService->getPiggyTowerChestRewardList($globalInfo->getUuid());
        // 查询用户已完成排名宝箱奖励
        $rank = [];
        $redis = bean('redis');
        $config = Config::loadJson('piggyTowerSpecialTask')['chest']['rank'];
        foreach ($config as $key => $conf) {
            $redisKey = $leagueRewardService->getRankChestTagKey($key, $globalInfo->getUuid());
            $tag = $redis->get($redisKey);
            if (!empty($tag)) {
                $rank [$key] = $tag;
            }
        }
        // 返回已领取的宝箱奖励
        $levels = [];
        $levelBoxReward = $leagueRewardService->getLevelBoxRewardList($globalInfo->getUuid());
        if (!empty($levelBoxReward)) {
            $levels = ArrayHelper::getColumn($levelBoxReward, 'level');
        }
        return [
            "code" => 1,
            "normal" => $normal,
            "rank" => $rank,
            "level" => $levels
        ];
    }

    /**
     * 获取某一个关卡的单关排行
     * @RequestMapping(route="getlevelrank")
     */
    public function actionGetLevelRank($level)
    {
        $globalInfo = globalInfo();
        $uuid = $globalInfo->getUuid();
        $leagueRankingService = bean(LeagueRankingService::class);
        $selfRank = $leagueRankingService->getUserLevelRank($level, $uuid);
        $top100 = $leagueRankingService->getTopNofLevelRank($level, $uuid);
        foreach ($top100 as $key => $levelOne) {
            if ($levelOne['user'] == $uuid) {
                $top100[$key]['name'] = bean(UserInfoDao::class)->getUsernameByUid($globalInfo->getUid());
            } else {
                $top100[$key]['name'] = Utils::randomName();
            }
        }
        return [
            'code' => 1,
            'selfRank' => $selfRank,
            'top100' => $top100
        ];
    }

    /**
     * 获得当前赛季的期名
     * @RequestMapping(route="season")
     */
    public function actionSeason()
    {
        $leagueService = bean(LeagueService::class);
        $season = $leagueService->currentOrNextSeason();
        $residualTime = $leagueService->startAndEndTime();
        return [
            'code' => 1,
            'season' => $season,
            'residualTime' => $residualTime
        ];
    }


}
