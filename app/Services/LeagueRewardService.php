<?php

namespace App\Services;

use App\Constants\RedisKey;
use App\Utils\Config;
use Swoft\Bean\Annotation\Bean;
use yii\helpers\ArrayHelper;


/**
 * @Bean()
 */
class LeagueRewardService
{
    /**
     * copy of task 以下 piggytower均指 league，为避免出错未改函数名称
     */

    /**
     * 获取piggyTower用户已完成的宝箱奖励列表
     */
    public function getPiggyTowerChestRewardList($uuid)
    {
        $key = $this->getChestRewardListKey($uuid);
        $redis = bean('redis');
        $list = $redis->get($key);
        if (empty($list)) {
            return [];
        }
        $tasks = json_decode($list, true);
        return ArrayHelper::getColumn($tasks, 'taskId');
    }

    /**
     * 设置piggyTower用户已完成的宝箱奖励列表
     */
    public function setPiggyTowerChestRewardList($reward, $uuid)
    {
        $key = $this->getChestRewardListKey($uuid);
        $redis = bean('redis');
        $listStr = $redis->get($key);
        if (empty($list)) {
            $list = [];
        } else {
            $list = json_decode($listStr, true);
        }
        $list [] = $reward;
        $redis->setEx($key, bean(LeagueService::class)::SEASON_INFO_VALID_TIME, json_encode($list));
    }

    /**
     * 判断用户是否可以领取条件为排名的宝箱，如果可以标记缓存
     */
    public function setTagOfRankChest($rank, $uuid)
    {
        $configAll = Config::loadJson("leagueTask");
        $config = $configAll["chest"]["rank"];
        foreach ($config as $key => $conf) {
            if ($conf [0] ["rank"] >= $rank) {
                $redis = bean('redis');
                $cacheKey = $this->getRankChestTagKey($key, $uuid);
                if (empty($redis->get($cacheKey))) {
                    $redis->setEx($cacheKey, bean(LeagueService::class)::SEASON_INFO_VALID_TIME, 0);
                }
            }
        }
    }

    /**
     * 根据所要求的条件，来判断用户是否达到要求
     */
    public function judgeComplete($type, $num, $uuid)
    {
        if ($type == 'star') {
            $nums = bean(LeagueService::class)->getAllStars($uuid);
            return ($nums >= $num);
        }
        if ($type == 'level') {
            bean(LeagueService::class)->initialize($uuid);
            $level = bean(LeagueService::class)->getNewLevel();
            return ($level >= $num);
        }
    }

    /**
     * 获取用户已经领取的piggyTower关卡奖励列表
     */
    public function getLevelBoxRewardList($uuid)
    {
        $key = $this->getLevelBoxRewardKey($uuid);
        $redis = bean('redis');
        $listStr = $redis->get($key);
        if (empty($listStr)) {
            return [];
        }
        $listArr = json_decode($listStr, true);
        return $listArr;
    }

    /**
     * 将某关卡加入已领取列表中
     */
    public function setLevelBoxRewardList($uuid, $level)
    {
        $rewardInfo = [];
        $key = $this->getLevelBoxRewardKey($uuid);
        $redis = bean('redis');
        $listStr = $redis->get($key);
        if (!empty($listStr)) {
            $rewardInfo = json_decode($listStr, true);
        }
        $reward = [
            'level' => $level,
            'rewardTime' => time()
        ];
        $rewardInfo [] = $reward;
        $redis->setEx($key, bean(LeagueService::class)::SEASON_INFO_VALID_TIME, json_encode($rewardInfo));
    }

// 	/**
// 	 * 获得奖牌数目的缓存键名
// 	 */
// 	public static function getMedalsSumKey($season, $uid) {
// 	    return 'league:' . $season . ":medalSum:$uid";
// 	}

    /**
     * 获取用户piggyTower本赛季宝箱奖励已领取列表
     */
    public function getChestRewardListKey($uuid)
    {
        return sprintf(RedisKey::LEAGUE_PIGGYTOWER_GETTED_LIST, bean(LeagueService::class)->currentOrNextSeason(), $uuid);
    }

    /**
     * 获取用户当前赛季，达到某排名的宝箱奖励的标记key
     */
    public function getRankChestTagKey($taskId, $uuid)
    {
        return sprintf(RedisKey::LEAGUE_REACH_RANKING, bean(LeagueService::class)->currentOrNextSeason(), $uuid, $taskId);
    }

    /**
     * 获取用户当前赛季，达到某关卡的奖励的标记key
     */
    public function getLevelBoxRewardKey($uuid)
    {
        return sprintf(RedisKey::LEAGUE_REACH_LEVEL, bean(LeagueService::class)->currentOrNextSeason(), $uuid);
    }
}