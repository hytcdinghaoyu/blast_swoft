<?php

/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2018/6/6
 * Time: 上午11:13
 */

namespace App\Services;

use App\Constants\RedisKey;
use App\Utils\ServerTime;
use Swoft\Db\Query;
use App\Models\Entity\Task;
use Swoft\Bean\Annotation\Bean;
use App\Models\Dao\TaskDao;


/**
 * @Bean()
 */
class NewUserActivityService
{

    public $activity_time = 9 * 86400;
    public $skinItems = ['5002'];


    /**
     * 返回当前用户剩余新手活动时间
     *
     * @return int
     */
    public function remainTime()
    {
        $globalInfo= globalInfo();
        $uid = $globalInfo->getUid();
        $find = Query::table(Task::class)->condition(['uid' => $uid, 'task_id'=>TaskDao::NEW_USER_ACTIVITY])->one()->getResult();
        if (empty($find)) {
            $remain_time = $this->activity_time;
        } elseif ($find['created_at'] + $this->activity_time > ServerTime::getTestTime()) {
            $remain_time = $find['created_at'] + $this->activity_time - ServerTime::getTestTime();
        } else {
            $remain_time = 0;
        }
        return max($remain_time,0);
    }

    /**
     * 如果用户开启活动，则开启它，返回活动信息
     * @return Task|array|null|\yii\db\ActiveRecord
     */
    public function creatIfNew()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $record = Task::findOne(['uid'=>$uid, 'task_id' , TaskDao::NEW_USER_ACTIVITY])->getResult();
        if (empty($record)) {
            $record = new Task();
            $record->setUid($uid);
            $record->setTaskId(TaskDao::NEW_USER_ACTIVITY);
            $record->setRewardTime(0);
            $record->setUpdatedAt(ServerTime::getTestTime());
            $record->setCreatedAt(ServerTime::getTestTime());
            $record->save()->getResult();
        }
        return $record;
    }

    /**
     * 标记该奖励领取过
     * @param $rewardid
     */
    public function tagReward($rewardId)
    {
        $key = $this->redisRewardKey();
        $redis = bean('redis');
        $redis->sAdd($key, $rewardId);
        $redis->EXPIRE($key, $this->activity_time);
    }

    /**
     * 是否领取过该奖励
     * @param $rewardid
     * @return bool
     */
    public function isRewarded($rewardId)
    {
        $key = $this->redisRewardKey();
        $redis =bean('redis');
        $isRewarded = $redis->sIsMember($key, $rewardId);
        if ($isRewarded) {
            return true;
        }
        return false;
    }

    /**
     * 返回用户领取过的所有奖励
     * @return mixed
     */
    public function rewardedList()
    {
        $key = $this->redisRewardKey();
        $redis = bean('redis');
        $rewardList = $redis->SMEMBERS($key);
        return $rewardList;
    }

    /**
     * 累计当前用户的活动总分数
     * @param $score
     */
    public function incrScore($score)
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $key = $this->redisScoreKey();
        $redis = bean('redis');
        $score_incred = $redis->ZINCRBY($key, $score, $uid);
        $redis->EXPIRE($key, $this->activity_time);
        return $score_incred;
    }

    /**
     * 获取当前用户的活动分数
     * @return int
     */
    public function getScore()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $key = $this->redisScoreKey();
        $redis = bean('redis');
        $score = $redis->zScore($key, $uid);
        return $score ? $score : 0;
    }

    /**
     * 领取结束奖励后结束活动，在task中记录领取时间
     */
    public function complete()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $record = Task::findOne(['uid'=>$uid , 'task_id'=>TaskDao::NEW_USER_ACTIVITY])->getResult();
        if (!empty($record)){
            $record->setRewardTime(ServerTime::getTestTime());
            $record->update()->getResult();
        }

    }

    public function redisRewardKey()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        return sprintf(RedisKey::NEW_USER_REWARD, $uid);
    }

    public function redisScoreKey()
    {
        return RedisKey::NEW_USER_REWARD_SCORE;
    }

    /**
     * 过滤皮肤类道具
     * @param $reward
     * @return array
     */
    public function filterSkinItem($reward)
    {
        foreach ($this->skinItems as $item) {
            unset($reward[$item]);
        }
        return $reward;
    }

    public function is_completed($score_reward)
    {
        $total = [];
        foreach ($score_reward as $k => $v) {
            $total[] = 'score_reward-' . $k;
        }
        if (count(array_intersect($this->rewardedList(), $total)) == count($score_reward)) {
            return true;
        }
        return false;
    }
}