<?php

namespace App\Services;

use App\Constants\LeagueRankType;
use App\Constants\RedisKey;
use App\Models\Dao\CenterActivityDao;
use App\Models\Dao\LeagueDao;
use App\Models\Entity\League;
use App\Utils\Config;
use App\Utils\ServerTime;
use Swoft\Bean\Annotation\Bean;
use App\Constants\ActivityType;
use yii\helpers\ArrayHelper;


/**
 * @Bean()
 */
class LeagueService
{
    //变量全是原League model中
    const UPDATE_SCORES_NEW_LEVEL = 1;
    const UPDATE_SCORES_OLD_LEVEL = 2;
    const SETTLEMENT_RESIDUAL_TIME = 3600; // 结算时间,2小时
    const SEASON_INFO_VALID_TIME = 5184000; // 缓存有效时间，暂定为30天
    const SEASON_STAY_TIME = 1209600; // 赛季持续时间两周
    const FIRST_PASS_LEVEL = 1;


    public $redis;
    public $levelKey; // 关卡信息的键名
    public $newLevelKey; // 最高关卡的键名
    public $allScoresKey; // 总分数键名
    public $newLevel; // 下一个关卡
    public $allScores; // 当前总分数
    public $levels; // 各关卡的信息
    public $uuid; // 当前用户的uuid


    //此处开始为原centerLeague model中方法
    /**
     * 算出时间戳所处的赛季，并计算出与当前赛季之间间隔了几个赛季
     */
    public function getIntervalSeasonNums($timePoint)
    {
        $now = ServerTime::getTestTime();
        $seasons = CenterActivityDao::getSeasonListByType(ActivityType::LEAGUE, 60);
        ArrayHelper::multisort($seasons, 'end_at', SORT_DESC);
        $currentSeason = '';
        $index = 0;
        $intervalSeason = [];
        foreach ($seasons as $season) {
            if ($season['start_at'] < $now && $season['end_at'] > $now) {
                $currentSeason = $season;
            }
            if ($season['start_at'] < $timePoint && $season['end_at'] > $timePoint) {
                $tagSeason = $season;
                break;
            }
            if (!empty($currentSeason)) {
                if ($index > 0) {
                    $intervalSeason [] = $season;
                }
                $index++;
            }
        }
        if (!isset($tagSeason)) {
            return false;
        }
        return [
            'tagSeason' => $tagSeason,
            'intervalSeason' => $intervalSeason
        ];
    }


    /**
     * 计算当前赛季的结束倒计时，若在结算期，计算下个赛季的开始时间
     */
    public function startAndEndTime()
    {
        $now = ServerTime::getTestTime();
        $season = $this->getSeason();
        if (empty($season)) { // 当前是结算期
            // 查询下个赛季的打开时间
            $nextSeason = $this->getNextSeason();
            if (!empty($nextSeason)) {
                $startTime = $nextSeason['start_at'] - $now;
                $endTime = $nextSeason['end_at'] - $now;
            }
        } else {

            // 计算当前赛季结束时间
            $endTime = $season['end_at'] - $now;
        }
        return [
            'startTime' => isset ($startTime) ? $startTime : 0,
            'endTime' => isset ($endTime) ? $endTime : 0
        ];
    }

    /**
     * 判断客户端赛季与当前赛季是否一致
     */
    public function seasonEqual($clientSeason)
    {
        $season = $this->getSeason();
        if (empty($season)) {
            return false;
        }
        return $clientSeason === $season['name'];
    }

    /**
     * 判断当前是不是结算时间
     */
    public function isSettlement()
    {
        $season = $this->getSeason();
        return $season === null;
    }

    /**
     * 客户端赛季时间
     */
    public function currentOrNextSeason()
    {
        $season = $this->getSeason();
        if ($season !== null) {
            return $season['name'];
        }
        $next = $this->getNextSeason();
        if ($next !== null) {
            return $next['name'];
        }
        return null;
    }

    /**
     * 获得上个赛季的名字
     */
    public function getPreSeasonName()
    {
        $pre = $this->getPreSeason();
        if ($pre !== null) {
            return $pre['name'];
        }
        return null;
    }

    /**
     * 找到当前赛季的信息
     */
    public function getSeason()
    {
        $now = ServerTime::getTestTime();
        $seasons = CenterActivityDao::getSeasonListByType(ActivityType::LEAGUE, 60);
        foreach ($seasons as $season) {
            if ($season['start_at'] < $now && $season['end_at'] > $now) {
                return $season;
            }
        }
    }

    /**
     * 找到下一个赛季的信息
     */
    public function getNextSeason()
    {
        $now = ServerTime::getTestTime();
        $seasons = CenterActivityDao::getSeasonListByType(ActivityType::LEAGUE, 60);
        ArrayHelper::multisort($seasons, 'start_at', SORT_ASC);
        foreach ($seasons as $season) {
            if ($season['start_at'] > $now) {
                return $season;
            }
        }
    }

    /**
     * 找到上一个赛季的信息
     */
    public function getPreSeason()
    {
        $now = ServerTime::getTestTime();
        $seasons = CenterActivityDao::getSeasonListByType(ActivityType::LEAGUE, 60);
        ArrayHelper::multisort($seasons, 'end_at', SORT_DESC);
        foreach ($seasons as $season) {
            if ($season['end_at'] < $now) {
                return $season;
            }
        }
    }


    //此处开始为原League方法

    public function getNameNumMap($rankNum)
    {

        $rankNum = (int)$rankNum;

        if ($rankNum > 6) {
            $rankNum = 6;
        }

        if ($rankNum < 0) {
            $rankNum = 0;
        }
        return LeagueRankType::valueToCamelStr($rankNum);

    }


    public function createInfo()
    {
        return [
            'rank' => LeagueRankType::Bronze,
            'upDown' => 0
        ];
    }

    /**
     * 获取 当前段位，如果没有记录，则设为青铜段位,判断是否是本赛季第一次打开上个赛季排名，如果是前25名则晋级，如果是后25名则降级
     */
    public function getRank($globalInfo)
    {

        $redis = bean('redis');
        $currentSeason = $this->getSeason()['name'];  //先找到当前赛季的名称
        if (empty($currentSeason)) {       //如果没有当前赛季的名称，那就找到上个赛季的名称
            $currentSeason = $this->getPreSeason()['name'];
        }

        $rankRedisKey = sprintf(RedisKey::LEAGUE_RANK_CUR, $currentSeason, $globalInfo->getUuid()); //拼接  得到当前用户的redis存储的key
        $rankDanInfo = $redis->get($rankRedisKey); //看redis里面有没有数据
        if (empty($rankDanInfo)) { //如果没有数据
            $info = League::findOne(['uid' => $globalInfo->getUid()])->getResult(); //查看user_info_extra表里面有没有当前用户的数据
            if (empty($info)) {   //没有就添加
                $this->initialize($globalInfo->getUuid());
                $createInfo = $this->createInfo();
                LeagueDao::createNewRecord($globalInfo->getUid(), $createInfo['rank'], $createInfo['upDown'], time(), ServerTime::getTestTime());
                $rankDanInfo = [
                    'upDown' => 0,
                    'rank' => LeagueRankType::Bronze
                ];
            } else {
                $startEnd = $this->getSeason();  //有数据的话，就查找当前赛季是否存在
                if (!empty($startEnd)) {    //存在当前赛季的话,判断一下时间是否过期，再把数据库里面的时间进行更改成当前赛季的时间段
                    if ($info->getUpdatedAt() >= $startEnd['start_at'] && $info->getUpdatedAt() <= $startEnd['end_at']) {
                        $rankDanInfo = [
                            'upDown' => 0,
                            'rank' => $info->getRank()
                        ];
                    } else {
                        /**
                         * @todo 根据更新时间来判断升降级
                         */
                        $preSeason = $this->getPreSeason();  //当前赛季没有就获取上个赛季
                        if (empty($preSeason)) {  //上个赛季也没有，就返回段位为0
                            $rankDanInfo = [
                                'upDown' => 0,
                                'rank' => $info->getRank()
                            ];
                        } else {    //算出时间戳所处的赛季，并计算出与当前赛季之间间隔了几个赛季
                            $intervalResult = $this->getIntervalSeasonNums($info->getUpdatedAt());
                            if (($intervalNum = count($intervalResult['intervalSeason'])) > 0) { //当前时间搓与上个赛季隔了1个或多个赛季，隔一个赛季，段位就减一，最小0
                                $infoRankCur = $info->getRank();
                                $infoRankCur -= $intervalNum;
                                if ($infoRankCur < 0) {
                                    $infoRankCur = 0;
                                }
                                $upDown = $infoRankCur - $info->getRank();
                                $rankDanInfo = [
                                    'upDown' => $upDown,
                                    'rank' => $infoRankCur
                                ];
                                LeagueDao::updateRecord($info, $infoRankCur, $upDown, ServerTime::getTestTime());
                            } else {
                                /**
                                 * 查看上个赛季成绩，并做出升降级判断
                                 */
                                $rank = bean(LeagueRankingService::class)->getPrevUserRank($globalInfo->getUuid(), $info->getRank());
                                $adjustConf = Config::loadJson('leagueAdjustCfg'); //查找配置文件的数据  .json
                                $rankName = self::getNameNumMap($info->getRank());
                                $upDownConf = $adjustConf[$rankName];
                                if (isset($rank['rank']) && ($rank['rank'] < $upDownConf['up'])) { //判断当前排名是否在前多少名，是否升级
                                    $tUpDown = 1;
                                    $tempRank = $info->getRank() + 1;
                                    if ($tempRank > 6) {
                                        $tempRank = 6;
                                        $tUpDown = 0;
                                    }
                                    LeagueDao::updateRecord($info, $tempRank, $tUpDown, ServerTime::getTestTime());

                                    $rankDanInfo = [
                                        'upDown' => $tUpDown,
                                        'rank' => $tempRank
                                    ];
                                } elseif (!isset($rank['rank']) || (100 - $rank['rank']) < $upDownConf['down']) { //没有上个赛季的信息，判断当前名次是否降级
                                    $tUpDown = -1;
                                    $tempRank = $info->getRank() - 1;
                                    if ($tempRank < 0) {
                                        $tempRank = 0;
                                        $tUpDown = 0;
                                    }
                                    LeagueDao::updateRecord($info, $tempRank, $tUpDown, ServerTime::getTestTime());
                                    $rankDanInfo = [
                                        'upDown' => $tUpDown,
                                        'rank' => $tempRank
                                    ];
                                } else {
                                    LeagueDao::updateRecord($info, "", "", ServerTime::getTestTime());
                                    $rankDanInfo = [
                                        'upDown' => 0,
                                        'rank' => $info->getRank()
                                    ];
                                }
                            }
                        }
                    }
                } else {
                    $rankDanInfo = [
                        'upDown' => 0,
                        'rank' => $info->getRank()
                    ];
                }
            }
            $redis->setEx($rankRedisKey, self::SEASON_INFO_VALID_TIME, json_encode($rankDanInfo));  //往redis里面存数据，当前用户的段位信息
        } else {
            $rankDanInfo = json_decode($rankDanInfo, true);
        }
        return $rankDanInfo;
    }


    public function initialize($uuid)
    {
        $this->uuid = $uuid;
        $this->redis = bean('redis');;
        $seasonName = $this->currentOrNextSeason();
        $this->levelKey = sprintf(RedisKey::LEAGUE_LEVEL_INFO, $seasonName, $uuid);
        $this->newLevelKey = sprintf(RedisKey::LEAGUE_TOP_LEVEL, $seasonName, $uuid);
    }

    /**
     * 另外的初始化方法
     */
    public function initializeBySeason($uuid, $seasonName)
    {
        $this->uuid = $uuid;
        $this->redis = bean('redis');
        $this->levelKey = sprintf(RedisKey::LEAGUE_LEVEL_INFO, $seasonName, $uuid);
        $this->newLevelKey = sprintf(RedisKey::LEAGUE_TOP_LEVEL, $seasonName, $uuid);
    }

    /**
     * 更新用户的关卡信息，更新最高关卡
     */
    public function incrLevelInfo($level, $star, $score)
    {
        $levelsString = $this->redis->get($this->levelKey);
        $levels = json_decode($levelsString, true);
        $this->levels = $levels;
        $now = time();
        $leagueRankingService = bean(LeagueRankingService::class);
        // 判断该用户是否已经存在piggyTower关卡信息
        if (empty($levelsString)) {
            // 如果不存在新建一个空数组
            $levels = [];
        } else if (array_key_exists($level, $levels)) { // 如果存在，并且已经存在要更新的关卡
            if ($levels [$level] ['score'] < $score) { // 如果本次得分大于以前的得分，更新的得分
                $diff = $score - $levels [$level] ['score'];
                $levels [$level] ['score'] = $score;
                $levels [$level] ['star'] = $star;
                $levels [$level] ['updated_at'] = $now;
                $this->redis->setEx($this->levelKey, self::SEASON_INFO_VALID_TIME, json_encode($levels));
                // 更新总排行榜
                $leagueRankingService->incrRankingList($diff);
                return true;
            }
            // 如果本次得分小于以前的得分，不需要更新，直接结束
            return null;
        }
        // 如果缓存中不存在本次要更新的关卡信息
        $levelInfo = [];
        $levelInfo ['level'] = $level;
        $levelInfo ['star'] = $star;
        $levelInfo ['score'] = $score;
        $levelInfo ['created_at'] = $now;
        $levelInfo ['updated_at'] = $now;
        $levels [$level] = $levelInfo;
        $this->redis->setEx($this->levelKey, self::SEASON_INFO_VALID_TIME, json_encode($levels));
        $this->setNewLevel($level); // 更新最新关卡
        // 更新排行榜
        $leagueRankingService->incrRankingList($score);
        return self::FIRST_PASS_LEVEL;
    }

    /**
     * 判断用户是不是第一次通过该关卡
     */
    public function firstPass($level)
    {
        $levelsString = $this->redis->get($this->levelKey);
        $levels = json_decode($levelsString, true);
        if (empty($levelsString)) {
            return true;
        }
        if (!array_key_exists($level, $levels)) {
            return true;
        }
        return false;
    }

    /**
     * 设置最新关卡
     */
    public function setNewLevel($newLevel)
    {
        $oldNewLevel = $this->getNewLevel();
        if ($oldNewLevel <= $newLevel) { // 如果跳关打也允许
            $this->redis->setEx($this->newLevelKey, self::SEASON_INFO_VALID_TIME, $newLevel + 1);
            $this->newLevel = $newLevel + 1;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获得最新关卡
     */
    public function getNewLevel()
    {
        if ($this->newLevel === null) {
            $this->newLevel = $this->redis->get($this->newLevelKey);
            if (empty($this->newLevel)) {
                $this->newLevel = 1;
            }
        }
        return $this->newLevel;
    }

    /**
     * 获取用户所有的关卡信息与总分数
     */
    public function getLevelInfo($uuid, $needTotalScores = false)
    {
        $levelInfo = $this->redis->get($this->levelKey);
        if (empty($levelInfo)) {
            return [];
        }
        $levelInfo = json_decode($levelInfo, true);
        if ($needTotalScores) {
            $leagueRankingService = bean(LeagueRankingService::class);
            $rank = $leagueRankingService->getUserRank($uuid);
            return [
                'levelInfo' => $levelInfo,
                'totalScores' => $rank['score'],
                'rank' => $rank['rank'] + 1
            ];
        }
        return [
            'levelInfo' => $levelInfo
        ];
    }

    /**
     * 判断用户是否领取过上赛季的排名奖励
     */
    public function rankRewardIsReceived($uuid)
    {
        $startTime = $this->isSettlement();
        $preSeason = $this->getPreSeason()['end_at'];
        $now = ServerTime::getTestTime();
        if ($startTime && ($now < ($preSeason + 1))) {//测试阶段3600改成1
            return true; //结算期一个小时之后，才允许用户领取奖励
        }
        $key = $this->getRankRewardTagKey($uuid);
        if (empty($key)) {
            return true;
        }
        $redis = bean('redis');
        $tag = $redis->get($key);
        return empty($tag) ? false : true;
    }

    /**
     * 标记用户已领取过上赛季的排名奖励
     */
    public function setRankReward($uuid)
    {
        $key = self::getRankRewardTagKey($uuid);
        $redis = bean('redis');
        $tag = $redis->setEx($key, self::SEASON_INFO_VALID_TIME, 1);
    }

    /**
     * 获得用户获得的总星数
     */
    public function getAllStars($uuid)
    {
        $key = $this->getLevelInfoKey($uuid);
        $redis = bean('redis');
        $infoStr = $redis->get($key);
        $star = 0;
        if (empty($infoStr)) {
            return $star;
        }
        $infoArr = json_decode($infoStr, true);
        foreach ($infoArr as $key => $info) {
            $star += $info ['star'];
        }
        return $star;
    }

    public function getRankRewardTagKey($uuid)
    {
        $name = $this->getPreSeasonName();
        if (empty($name)) {
            return "";
        }
        return sprintf(RedisKey::LEAGUE_RANK_REWARD_TAG, $name, $uuid);
    }

    public function getLastSeason($uuid, $season = null)
    {
        $key = $this->getLastSeasonKey($uuid);
        $redis = bean('redis');
        if ($season != null) {
            $redis->set($key, $season);
        }
        $lastSeason = $redis->get($key);
        if (empty($lastSeason)) {
            return "";
        }
        return $lastSeason;
    }

    /**
     * 检查该用户是否存在联赛赛季信息
     */
    public function checkLeagueInfo($uid)
    {
        return LeagueDao::checkLeagueInfo($uid);
    }

    public function getLevelInfoKey($uuid)
    {
        return sprintf(RedisKey::LEAGUE_LEVEL_INFO, $this->currentOrNextSeason(), $uuid);
    }

    public function getLastSeasonKey($uuid)
    {
        return sprintf(RedisKey::LEAGUE_LAST_SEASON, $uuid);
    }


}