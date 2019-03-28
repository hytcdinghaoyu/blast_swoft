<?php

namespace App\Services;

use App\Constants\LeagueRankType;
use App\Constants\PunishType;
use App\Constants\RedisKey;
use App\Models\PunishDetail;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;


/**
 * @Bean()
 */
class LeagueRankingService
{
    const USER_SPEND_THRESHOLD = 0; //潘一天暂定阈值

    const USER_IS_RICH = 1;

    const USER_NOT_RICH = 0;

    const GROUP_GOLD_PT_RICH_NUMBER = 3;

    const GROUP_GOLD_RICH_NUMBER = 20;

    const GROUP_PT_RICH_NUMBER = 40;

    const GROUP_TOTAL_USER_NUMBER = 100;

    const GLOBAL_TOP_N = 100;

    const CLIENT_RANK_CACHE_TIME = 7200; // 查询排行榜缓存时间

    const RANK_CACHE_TIME = 5184000; // 排行榜有效期，60天

    const GOLDEN_LEVEL_CONDITION = 100; //金色关卡条件，前100名



    /**
     * 更新排行榜排名
     *
     * @param string $customerId
     * @param int $score
     */
    public function incrRankingList($score)
    {
        $seasonsKey = sprintf(RedisKey::LEAGUE_RANKING, bean(LeagueService::class)->currentOrNextSeason());// 赛季期数
        $writeRedis = $this->getRankRedis();
        $globalInfo = globalInfo();
        $groupRankKey = $this->getGroupRankKey($globalInfo);
        $punishObj = PunishDetail::findOne(['openId' => $globalInfo->getThirdId(), 'type' => PunishType::BAN_RANKING_LEAGUE]);
        if ($punishObj) {
            $writeRedis->zRem($groupRankKey, $globalInfo->getUuid());
        } else {
            $writeRedis->zIncrBy($groupRankKey, $score, $globalInfo->getUuid());//正常加分
        }
        $writeRedis->expire($groupRankKey, self::RANK_CACHE_TIME);
        /**
         * 维护一份总榜
         */
        if ($punishObj) {
            $writeRedis->zRem($seasonsKey, $globalInfo->getUuid());
        } else {
            $writeRedis->zIncrBy($seasonsKey, $score, $globalInfo->getUuid());//正常加分
        }
        $writeRedis->expire($seasonsKey, self::RANK_CACHE_TIME);
        /**
         * @todo 检查排行榜，填充机器人，替换机器人
         */
        $this->checkBot($groupRankKey);
    }

    /**
     * 更新单关排行榜
     */
    public function updateLevelRankingList($level, $score, $uuid)
    {
        $writeRedis = bean('redis');
        $key = $this->getLevelRankKey($level);
        $result = $writeRedis->zAdd($key, $score, $uuid);
        $writeRedis->expire($key, self::RANK_CACHE_TIME);
    }

    /**
     * 获得单关排行榜前100名,并检查用户是否进入前100名
     */
    public function getTopNofLevelRank($level, $uuid)
    {
        $rankKey = $this->getLevelRankKey($level);
        $readRedis = bean('redis');
        $rank = [];
        $temp = [];
        $users = $readRedis->zrevrange($rankKey, 0, static::GOLDEN_LEVEL_CONDITION - 1, true);
        foreach ($users as $kUuid => $score) {
            $temp['user'] = $kUuid;
            $temp['score'] = (int)$score;
            $rank [] = $temp;
        }
        $goldenUuids = array_keys($users);
        if (in_array($uuid, $goldenUuids, true)) {
            $goldenKey = $this->getGoldenLevelKey($uuid);
            $redis = bean('redis');
            $goldenStr = $redis->get($goldenKey);
            if (empty($goldenStr)) {
                $goldenArr = [];
            } else {
                $goldenArr = json_decode($goldenStr, true);
            }
            if (!in_array($level, $goldenArr, true)) {
                $goldenArr[] = $level;
            }
            $redis->setEx($goldenKey, bean(LeagueService::class)::SEASON_INFO_VALID_TIME, json_encode($goldenArr));
        }
        return $rank;
    }

    /**
     * 检查金色关卡
     */
    public function checkGoldenLevel($uuid)
    {
        $readRedis = bean('redis');
        $redis = bean('redis');
        $goldenKey = $this->getGoldenLevelKey($uuid);
        $glodenLevelStr = $redis->get($goldenKey);
        if (empty($glodenLevelStr)) {
            return [];
        }

        $goldenLevel = json_decode($glodenLevelStr, true);
        foreach ($goldenLevel as $key => $level) {
            //查询在该关卡的排名
            $levelKey = $this->getLevelRankKey($level);
            $rank = $readRedis->zRevRank($levelKey, $uuid);
            if ($rank > static::GOLDEN_LEVEL_CONDITION - 1) {
                unset($goldenLevel[$key]);
            }
        }
        $newGolden = array_values($goldenLevel);
        $redis->setEx($goldenKey, bean(LeagueService::class)::SEASON_INFO_VALID_TIME, json_encode($newGolden));
        return $newGolden;
    }

    /**
     * 获得全球排行榜前N名
     */
    public function getTopNOfRanking()
    {
        $globalInfo = globalInfo();
        $key = $this->getGroupRankKey($globalInfo);
        $readRedis = $this->getRankRedis();
        /**
         * 更新机器人
         */
        $this->checkBot($key);
        $topN = [];
        $item = [];
        $users = $readRedis->zrevrange($key, 0, 100, true);
        foreach ($users as $user => $score) {
            $item ['user'] = $user;
            $item ['score'] = (int)$score;
            $topN [] = $item;
        }
        return $topN;
    }

    /**
     * 获得全球排行榜前N名
     */
    public function gmGetTopNOfRanking()
    {
        if (bean(LeagueService::class)->isSettlement()) {
            return [];
        }
        $globalInfo = globalInfo();
        $key = $this->getGroupRankKey($globalInfo);
        $readRedis = $this->getRankRedis();
        /**
         * 更新机器人
         */
        $this->checkBot($key);
        $topN = [];
        $item = [];
        $users = $readRedis->zrevrange($key, 0, 100, true);
        foreach ($users as $user => $score) {
            $item ['user'] = $user;
            $item ['score'] = (int)$score;
            $topN [] = $item;
        }
        return $topN;
    }

    /**
     * 获得上个赛季排行榜
     */
    public function getPreRankList($rankDan)
    {
        $globalInfo = globalInfo();
        $season = bean(LeagueService::class)->getPreSeason();
        if (!empty($this->checkUserInfoBySeason($season))) {
            $groupRankKey = $this->getGroupRankKey($globalInfo, $season, $rankDan);
            $readRedis = $this->getRankRedis();
            $topN = [];
            $item = [];
            $users = $readRedis->zrevrange($groupRankKey, 0, 100, true);
            foreach ($users as $user => $score) {
                $item ['user'] = $user;
                $item ['score'] = (int)$score;
                $topN [] = $item;
            }
        } else {
            $topN = [];
        }

        return $topN;
    }

    /**
     * 获得指定用户的本组排名与分数
     */
    public function getUserRank($uuid)
    {
        $globalInfo = globalInfo();
        $groupRankKey = $this->getGroupRankKey($globalInfo);
        $readRedis = $this->getRankRedis();
        /**
         * 更新机器人
         */
        $this->checkBot($groupRankKey);
        $rank = [];
        $rank ['rank'] = $readRedis->zRevRank($groupRankKey, $uuid);
        $rank ['score'] = $readRedis->zScore($groupRankKey, $uuid);
        return $rank;
    }

    /**
     * 获取用户上个赛季的排名与分数
     */
    public function getPrevUserRank($uuid, $rankDan)
    {
        $globalInfo = globalInfo();
        $season = bean(LeagueService::class)->getPreSeason();
        $groupRankKey = $this->getGroupRankKey($globalInfo, $season, $rankDan);
        if (empty($groupRankKey)) {
            return [
                'rank' => '',
                'score' => ''
            ];
        }
        $readRedis = $this->getRankRedis();
        $rank = [];
        $rank ['rank'] = $readRedis->zRevRank($groupRankKey, $uuid);
        $rank ['score'] = $readRedis->zScore($groupRankKey, $uuid);
        return $rank;
    }

    /**
     * 获得指定用户的单关排名与分数
     */
    public function getUserLevelRank($level, $uuid)
    {
        $readRedis = bean('redis');
        $rank = [];
        $key = $this->getLevelRankKey($level);
        if ($key == null) {
            return null;
        }
        $ranking = $readRedis->zRevRank($key, $uuid);//空的返回null,不然返回0或1，2，3
        $rank['rank'] = isset($ranking)? $ranking+1 : $ranking + 1;
        $rank ['score'] = $readRedis->zScore($key, $uuid);
        return $rank;
    }

    public function getGroupRankKey($globalInfo, $season = [], $rankDan = '')
    {
        if (!isset($rankDan)) {
            $rankDan = bean(LeagueService::class)->getRank($globalInfo)['rank'];
        }
        if (empty($season)) {
            $season = bean(LeagueService::class)->getSeason();
            if (empty($season)) {
                $season = bean(LeagueService::class)->getPreSeason();
            }
            $groupInfo = $this->getUserGroupNumber($rankDan, $globalInfo);
        } else {
            $groupInfo = $this->getUserGroupNumber($rankDan, $globalInfo, $season);
            if (empty($groupInfo)) {
                return false;
            }
        }
        return $this->getUserGroupRankKey($season['name'], $rankDan, $groupInfo['groupNumber'], $groupInfo['isRich']);
    }

    /**
     * 获取用户的组别编号和是否有钱标签
     */
    private function getUserGroupNumber($rankDan, $globalInfo, $seasonPreset = [])
    {
        $season = $seasonPreset;
        if (empty($season)) {
            $season = bean(LeagueService::class)->getSeason();
            if (empty($season)) {
                $season = bean(LeagueService::class)->getPreSeason();
            }
        }
        $groupInfoKey = $this->getUserGroupInfoKey($season['name'], $globalInfo->getUuid());
        $numberRedis = bean('redis');
        $groupInfo = $numberRedis->get($groupInfoKey);
        if (empty($groupInfo)  && empty($seasonPreset)) {
            //分配用户的组别号以及是否是有钱人
            $groupInfo = $this->deliverUserGroupNumber($rankDan, $globalInfo);
            $numberRedis->setEx($groupInfoKey, static::RANK_CACHE_TIME, json_encode($groupInfo));
            return $groupInfo;
        } else if (!empty($seasonPreset) && empty($groupInfo)) {
            //分配用户的组别号以及是否是有钱人
            $groupInfo = $this->deliverUserGroupNumber($rankDan, $globalInfo, $season);
            $numberRedis->setEx($groupInfoKey, static::RANK_CACHE_TIME, json_encode($groupInfo));
            return $groupInfo;
        }
        return json_decode($groupInfo, true);
    }

    /**
     * 分配用户的组别号和是否有钱标签
     */
    private function deliverUserGroupNumber($rankDan, $globalInfo, $season = [])
    {

        $mnow = Utils::mtime();
        /**
         * 获取用户活动开启前七天消费情况
         */
        //获取当前赛季开启时间
        if (empty($season)) {
            $season = bean(LeagueService::class)->getSeason();
            if (empty($season)) {
                return false;
            }
        }

        /**
         * 获取用户应该所在的用户总榜key
         */
        if ($rankDan > LeagueRankType::Platinum) {
            $userIsRich = static::USER_IS_RICH;
            $totalUserListKey = $this->getRankDanAllListKey($season['name'], $rankDan, static::USER_IS_RICH);
        } else {
            //$spend = IapOrder::getSpendByTime($globalInfo->uid, $season["start_at"], 30);
            $spend = 0;
            $userIsRich = ($spend > static::USER_SPEND_THRESHOLD) ? static::USER_IS_RICH : static::USER_NOT_RICH;
            $totalUserListKey = $this->getRankDanAllListKey($season['name'], $rankDan, $userIsRich);
        }
        $totalUserListRedis = $this->getRankRedis();

        /**
         * 入总榜初始化
         */
        if ($totalUserListRedis->TTL($totalUserListKey) == -1) {//存在key但没有设置时间
            $totalUserListRedis->expire($totalUserListKey, self::RANK_CACHE_TIME);
        }
        $rankNumber = $totalUserListRedis->zRank($totalUserListKey, $globalInfo->getUuid());
        if (!isset($rankNumber)) {
            $totalUserListRedis->zAdd($totalUserListKey, $mnow, $globalInfo->getUuid());
            $rankNumber = $totalUserListRedis->zRank($totalUserListKey, $globalInfo->getUuid());
        }

        if ($rankDan <= LeagueRankType::Silver) { //青铜、白银
            $groupNumber = floor($rankNumber / static::GROUP_TOTAL_USER_NUMBER);
        } elseif ($rankDan <= LeagueRankType::Gold) { //黄金
            if ($userIsRich) {
                $groupNumber = floor($rankNumber / static::GROUP_GOLD_RICH_NUMBER);
            } else {
                $groupNumber = floor($rankNumber / (static::GROUP_TOTAL_USER_NUMBER - static::GROUP_GOLD_RICH_NUMBER));
            }
        } elseif ($rankDan <= LeagueRankType::Platinum) { //铂金
            if ($userIsRich) {
                $groupNumber = floor($rankNumber / static::GROUP_PT_RICH_NUMBER);
            } else {
                $groupNumber = floor($rankNumber / (static::GROUP_TOTAL_USER_NUMBER - static::GROUP_PT_RICH_NUMBER));
            }
        } elseif ($rankDan <= LeagueRankType::Master) { //钻石、神圣   小于最新的段位6
            $groupNumber = floor($rankNumber / static::GROUP_TOTAL_USER_NUMBER);
        }

        return [
            "groupNumber" => $groupNumber,
            "isRich" => $userIsRich
        ];
    }

    /**
     * 获取用户的组别编号和真实的是否有钱标签
     */
    public function getRealGroupInfo()
    {
        $globalInfo = globalInfo();
        $rankDan = bean(LeagueService::class)->getRank($globalInfo)['rank'];
        if (empty($season)) {
            $season = bean(LeagueService::class)->getSeason();
            if (empty($season)) {
                $season = bean(LeagueService::class)->getPreSeason();
            }
            $groupInfo = $this->getUserGroupNumber($rankDan, $globalInfo);
        } else {
            $groupInfo = $this->getUserGroupNumber($rankDan, $globalInfo, $season);
            if (empty($groupInfo)) {
                return false;
            }
        }

        // 修正大于白金的isRich默认USER_IS_RICH
        if ($rankDan > LeagueRankType::Platinum) {
            //$spend = IapOrder::getSpendByTime($globalInfo->uid, $season["start_at"], 30);
            $spend = 0;
            $groupInfo['isRich'] = ($spend > static::USER_SPEND_THRESHOLD) ? static::USER_IS_RICH : static::USER_NOT_RICH;
        }

        $groupInfo['season'] = $season['name'];
        $groupInfo['rankDan'] = $rankDan;
        return $groupInfo;
    }

    /**
     * 排行榜中机器人检查
     */
    public function checkBot($groupKey)
    {
        $now = ServerTime::getTestTime();
        $redis = $this->getRankRedis();
        $total = $redis->zCard($groupKey);

        $botArray = [
            'bot-fixed-70000-' => 0,
            'bot-fixed-135000-' => 0,
            'bot-fixed-160000-' => 0,
            'bot-float-160000-160000-1050000-' => 0,
            'bot-float-160000-100000-850000-' => 0,
            'bot-float-135000-85000-700000-' => 0,
            'bot-float-135000-70000-600000-' => 0
        ];
        $users = $redis->zrevrange($groupKey, 0, $total, true);

        if ($total < 100) { //数量小于100说明未初始化机器人,遍历，初始化
            /**
             * 可替换机器人初始化
             */
            $fixBotIndex = [
                'bot-fixed-70000-' => 0,
                'bot-fixed-135000-' => 0,
                'bot-fixed-160000-' => 0,
            ];
            if ($total >= 60) {
                $fixBotIndex['bot-fixed-70000-'] = 100 - $total;
            } elseif ($total >= 30) {
                $fixBotIndex['bot-fixed-70000-'] = 40;
                $fixBotIndex['bot-fixed-135000-'] = 60 - $total;
            } elseif ($total >= 10) {
                $fixBotIndex['bot-fixed-70000-'] = 40;
                $fixBotIndex['bot-fixed-135000-'] = 30;
                $fixBotIndex['bot-fixed-160000-'] = 30 - $total;
            } else {
                $fixBotIndex['bot-fixed-70000-'] = 40;
                $fixBotIndex['bot-fixed-135000-'] = 30;
                $fixBotIndex['bot-fixed-160000-'] = 20;
            }

            $bot_70000_index = 41;
            $bot_135000_index = 31;
            $bot_160000_index = 21;//假如该类型机器人一个都没有，那么从21-1=20开始遍历添加
            foreach ($users as $kUser => $vScore) {
                if (strpos($kUser, 'bot-fixed-70000-') !== false) {
                    $tmp = substr($kUser, strlen('bot-fixed-70000-'));
                    $bot_70000_index = $bot_70000_index < $tmp ? $bot_70000_index : $tmp;
                }
                if (strpos($kUser, 'bot-fixed-135000-') !== false) {
                    $tmp = substr($kUser, strlen('bot-fixed-135000-'));
                    $bot_135000_index = $bot_135000_index < $tmp ? $bot_135000_index : $tmp;
                }
                if (strpos($kUser, 'bot-fixed-160000-') !== false) {
                    $tmp = substr($kUser, strlen('bot-fixed-160000-'));
                    $bot_160000_index = $bot_160000_index < $tmp ? $bot_160000_index : $tmp;
                }
            }

            foreach ($fixBotIndex as $kIndex => $vNum) {
                if ($kIndex == 'bot-fixed-70000-') {
                    $minNum = max(1, $bot_70000_index - $vNum);
                    for ($i = $bot_70000_index - 1; $i >= $minNum; $i--) {
                        $redis->zAdd($groupKey, 70000, $kIndex . $i);
                    }
                }
                if ($kIndex == 'bot-fixed-135000-') {
                    $minNum = max(1, $bot_135000_index - $vNum);
                    for ($i = $bot_135000_index - 1; $i >= $minNum; $i--) {
                        $redis->zAdd($groupKey, 135000, $kIndex . $i);
                    }
                }
                if ($kIndex == 'bot-fixed-160000-') {
                    $minNum = max(1, $bot_160000_index - $vNum);
                    for ($i = $bot_160000_index - 1; $i >= $minNum; $i--) {
                        $redis->zAdd($groupKey, 160000, $kIndex . $i);
                    }
                }

            }


        } elseif ($total > 100) { //数量大于100说明有机器人需要剔除
            //添加机器人编号从大到小  剔除机器人编号从小到大
            $diff = $total - 100;
            $delNum = 0;
            $bot_70000_index = 40;
            $bot_135000_index = 30;
            $bot_160000_index = 20;
            foreach ($users as $kUser => $vScore) {
                if (strpos($kUser, 'bot-fixed-70000-') !== false) {
                    $tmp = substr($kUser, strlen('bot-fixed-70000-'));
                    $bot_70000_index = $bot_70000_index < $tmp ? $bot_70000_index : $tmp;
                }
                if (strpos($kUser, 'bot-fixed-135000-') !== false) {
                    $tmp = substr($kUser, strlen('bot-fixed-135000-'));
                    $bot_135000_index = $bot_135000_index < $tmp ? $bot_135000_index : $tmp;
                }
                if (strpos($kUser, 'bot-fixed-160000-') !== false) {
                    $tmp = substr($kUser, strlen('bot-fixed-160000-'));
                    $bot_160000_index = $bot_160000_index < $tmp ? $bot_160000_index : $tmp;
                }
            }
            krsort($users);//保证先剔除70000的再剔除135000，160000
            foreach ($users as $kUser => $vScore) {
                if (strpos($kUser, 'bot-fixed-70000-') !== false) {
                    $redis->zRem($groupKey, 'bot-fixed-70000-' . $bot_70000_index);
                    $bot_70000_index++;
                    $delNum++;
                    if ($delNum >= $diff) {
                        break;
                    }
                    continue;
                }
                if (strpos($kUser, 'bot-fixed-135000-') !== false) {
                    $redis->zRem($groupKey, 'bot-fixed-135000-' . $bot_135000_index);
                    $bot_135000_index++;
                    $delNum++;
                    if ($delNum >= $diff) {
                        break;
                    }
                    continue;
                }
                if (strpos($kUser, 'bot-fixed-160000-') !== false) {
                    $redis->zRem($groupKey, 'bot-fixed-160000-' . $bot_160000_index);
                    $bot_160000_index++;
                    $delNum++;
                    if ($delNum >= $diff) {
                        break;
                    }
                    continue;
                }
            }
        }

        /**
         * 初始化
         */
        $season = bean(LeagueService::class)->getSeason();
        if (empty($season)) {
            $season = bean(LeagueService::class)->getPreSeason();
        }
        /**
         * 不可替换机器人初始化
         */

        $botIndex = 'bot-float-160000-160000-1050000-1';
        $fadeScore = 160000 + (round(($now - $season['start_at']) / (3600 * 6), 2) * 16000 + $this->botConfig($botIndex, 1));
        if ($fadeScore > 1050000) {

            $fadeScore = 1050000 + $this->botConfig($botIndex, 1);
        }

        $redis->zAdd($groupKey, $fadeScore, $botIndex);
        for ($i = 1; $i <= 2; $i++) {
            $botIndex = 'bot-float-160000-100000-850000-';
            $fadeScore = 160000 + (round(($now - $season['start_at']) / (3600 * 6), 2) * 10000 + $this->botConfig($botIndex, $i));
            if ($fadeScore > 850000) {
                $fadeScore = 850000 + $this->botConfig($botIndex, $i);
            }
            $botIndex .= $i;
            $redis->zAdd($groupKey, $fadeScore, $botIndex);
        }
        for ($i = 1; $i <= 3; $i++) {
            $botIndex = 'bot-float-135000-85000-700000-';
            $fadeScore = 135000 + (round(($now - $season['start_at']) / (3600 * 6), 2) * 85000 + $this->botConfig($botIndex, $i));
            if ($fadeScore > 700000) {
                $fadeScore = 700000 + $this->botConfig($botIndex, $i);
            }
            $botIndex .= $i;
            $redis->zAdd($groupKey, $fadeScore, $botIndex);
        }
        for ($i = 1; $i <= 4; $i++) {
            $botIndex = 'bot-float-135000-70000-600000-';
            $fadeScore = 135000 + (round(($now - $season['start_at']) / (3600 * 6), 2) * 70000 + $this->botConfig($botIndex, $i));
            if ($fadeScore > 600000) {
                $fadeScore = 600000 + $this->botConfig($botIndex, $i);
            }
            $botIndex .= $i;
            $redis->zAdd($groupKey, $fadeScore, $botIndex);
        }
    }

    public function getBotName($groupNumber)
    {//每个分组保证机器人名字不一样
        $botArray = [
            'bot-fixed-70000-' => 40,
            'bot-fixed-135000-' => 30,
            'bot-fixed-160000-' => 20,
            'bot-float-160000-160000-1050000-' => 1,
            'bot-float-160000-100000-850000-' => 2,
            'bot-float-135000-85000-700000-' => 3,
            'bot-float-135000-70000-600000-' => 4
        ];
        $redis = $this->getRankRedis();
        $key = sprintf(RedisKey::LEAGUE_BOT_NAME, $groupNumber);
        $nameArr = $redis->hGetAll($key);
        if (empty($nameArr)) {
            $arrTmp = [];
            foreach ($botArray as $bot => $num) {
                for ($i = 1; $i <= $num; $i++) {
                    $arrTmp[$bot . $i] = Utils::randomName();
                }
            }
            $redis->hMSet($key, $arrTmp);
            $nameArr = $arrTmp;
            //设置过期时间
            $curSeason = bean(LeagueService::class)->getSeason();
            if (isset($curSeason['start_at'])) {
                $expireTime = $curSeason['end_at'] - $curSeason['start_at'];
                $redis->expire($key, $expireTime);
            } else {
                $redis->expire($key, bean(LeagueService::class)::SEASON_STAY_TIME);
            }
        }
        return $nameArr;
    }

    public static function botConfig($botIndex, $zIndex)
    {
        $botFloatScore = [
            'bot-float-135000-70000-600000-' => [
                1 => 1120,
                2 => 3230,
                3 => 5450,
                4 => 7580
            ],
            'bot-float-135000-85000-700000-' => [
                1 => 1210,
                2 => 4340,
                3 => 6660
            ],
            'bot-float-160000-100000-850000-' => [
                1 => 2730,
                2 => 7840
            ],
            'bot-float-160000-160000-1050000-1' => [
                1 => 6560
            ]
        ];
        return $botFloatScore[$botIndex][$zIndex];
    }


    public function getRankRedis()
    {
        return bean('redis');
    }

    public function checkUserInfoBySeason($season)
    {
        $globalInfo = globalInfo();
        $groupInfoKey = $this->getUserGroupInfoKey($season['name'], $globalInfo->getUuid());
        $numberRedis = bean('redis');
        return $groupInfo = $numberRedis->get($groupInfoKey);
    }

    public function getRankKey($season, $rankDan, $group)
    {
        return sprintf(RedisKey::LEAGUE_RANKING_GROUP, $season, $rankDan, $group);
    }

    public function getLevelRankKey($level)
    {
        return sprintf(RedisKey::LEAGUE_LEVEL_RANKING, bean(LeagueService::class)->currentOrNextSeason(), $level);
    }

    public function getGoldenLevelKey($uuid)
    {
        return sprintf(RedisKey::LEAGUE_GOLDEN_LEVEL, bean(LeagueService::class)->currentOrNextSeason(), $uuid);
    }

    public function getRankDanAllListKey($season, $rankDan, $isRich)
    {
        return sprintf(RedisKey::LEAGUE_ALL_USER_LIST, $season, $rankDan, $isRich);
    }

    public function getUserGroupRankKey($season, $rankDan, $groupNumber, $isRich = 0)
    {
        if ($rankDan <= LeagueRankType::Silver) {//青铜白银，有钱和没钱用户分开分组
            return sprintf(RedisKey::LEAGUE_USER_GROUP_RANK_MONEY, $season, $rankDan, $isRich, $groupNumber);
        }
        return sprintf(RedisKey::LEAGUE_USER_GROUP_RANK, $season, $rankDan, $groupNumber);
    }

    public function getUserGroupInfoKey($season, $uuid)
    {
        return sprintf(RedisKey::LEAGUE_USER_GROUP_INFO, $season, $uuid);
    }
}