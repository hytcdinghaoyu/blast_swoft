<?php

namespace App\Models\Dao;


use App\Constants\MoneyType;
use App\Constants\RedisKey;
use App\Models\Entity\Task;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Utils\Utils;
use Swoft\Db\Query;
use Swoft\Bean\Annotation\Bean;
use yii\helpers\BaseArrayHelper;


/**
 *
 * @Bean()
 */
class TaskDao
{
    const NEW_USER_ACTIVITY = 200001;
    /**
     * 找到指定奖励包已奖励的次数
     * @return int;
     */
    public function getCountById($uid, $packageId)
    {
        $all = $this->findAll($uid);
        $count = 0;
        foreach ($all as $kItem) {
            if ($kItem['task_id'] == $packageId) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * 找到用户所有的奖励bao
     */
    public function findAll($uid)
    {
        $cache = bean('cache');
        $cacheKey = $this->getAllCacheKey($uid);
        $all = $cache->get($cacheKey);
        if (empty($all)) {
            $tasks = Query::table(Task::class)->condition(['uid' =>$uid,['task_id', '>=', 1000000],['task_id', '<=', 1001000]])->get()->getResult();
            if (!empty($tasks)) {
                $cache->set($cacheKey, json_encode($tasks), 7 * 86400); //暂定7天
                return $tasks;
            }
            return [];
        }
        return json_decode($all,true);
    }

    /**
     * 删除任务缓存
     */
    public function delCache($uid)
    {
        $cache = bean('cache');
        $cacheKey = $this->getAllCacheKey($uid);;
        $cache->delete($cacheKey);
    }

    /**
     * 获得所有奖励包缓存key
     */
    private function getAllCacheKey($uid)
    {
        return sprintf(RedisKey::REWARD_BAG, $uid);
    }

    public function createOne($uid,$task_id)
    {
        $task = new Task();
        $task->setUid($uid);
        $task->setTaskId($task_id);
        $task->setRewardTime(time());
        $task->setCreatedAt(time());
        $task->setUpdatedAt(time());
        $task->save()->getResult();
    }

    public function findOne($uid,$task_id)
    {
        return Utils::formatArrayValue(Query::table(Task::class)->condition(['uid'=>$uid,'task_id'=>$task_id])->one()->getResult());
    }


    public static function findAllCommon($uid, $fromDb = false)
    {
        if ($fromDb) {
            return static::findAllCommonFromDb($uid);
        }
        // 目前使用redisMaster, redis用途：缓存任务列表，时间12小时
        $redis = bean('redis');
        /**
         * redis不可用时，取数据库
         */
        if ($redis === false) {
            return static::findAllCommonFromDb($uid);
        }

        $key = 'all:task:' . $uid;
        $json = $redis->get($key);
        if ($json) {
            return json_decode($json);
        }

        $tasks = static::findAllCommonFromDb($uid);

        // task/list请求量是init的3倍多，说明一次启动掉用过3次以上task/list接口，过期时间12小时基本可以确保一次游戏命中两次缓存
        // 最终的过期时间根据请求量和服务器调整
        $redis->setEx($key, 43200, json_encode($tasks));
        return $tasks;
    }

    public static function findAllCommonFromDb($uid)
    {
        $tasks = Query::table(Task::class)->condition(['uid'=>$uid,['task_id', '>=',  10000],
            ['task_id', '<=', 10999]])->get(["task_id"])->getResult();

        return $tasks;
    }

    /**
     * 获取所有已完成的主线任务
     */
    public static function findAllMaster($uid, $fromDb = false)
    {
        $redis = bean('redis');
        $key = static::getKeyByType("masterFinished", $uid);
        /**
         * redis不可用
         */
        if ($redis === false) {
            return static::findAllMasterFromDb($uid);
        }
        if ($fromDb) {
            $list = static::findAllMasterFromDb($uid);
            $redis->setEx($key, 86400, json_encode($list));
            return $list;
        }
        $json = $redis->get($key);
        if ($json) {
            $list = json_decode($json, true);
            //由于上线前在线上进行测试个别测试账户出现垃圾数据,这里为了安全，做去重处理
            static::uniqueAndReset($list);
            return $list;
        }

        $tasks = static::findAllMasterFromDb($uid);
        // task/list请求量是init的3倍多，说明一次启动掉用过3次以上task/list接口，过期时间5小时基本可以确保一次游戏命中两次缓存
        // 最终的过期时间根据请求量和服务器调整
        $redis->setEx($key, 86400, json_encode($tasks));
        return $tasks;
    }

    public static function findAllMasterFromDb($uid)
    {
        $range = static::getRangeOfTask();
        $tasks = Query::table(Task::class)->condition(['uid'=>$uid,['task_id', '>=',  $range['lowLimit']],
            ['task_id', '<=', $range['upLimit']]])->get(["task_id"])->getResult();

        $tasks = BaseArrayHelper::getColumn($tasks, 'task_id');
        //由于上线前在线上进行测试个别测试账户出现垃圾数据,这里为了安全，做去重处理
        static::uniqueAndReset($tasks);
        return Utils::formatArrayValue($tasks);
    }

    public static function findUsedInRewardByType($type,$uid,$taskId,$rewardTime){
        if ($type == 'daily'){
            return Utils::formatArrayValue(Query::table(Task::class)->condition(['uid' => $uid, 'task_id' => $taskId,
                'reward_time' => strtotime($rewardTime)])->one()->getResult());


        }elseif($type == 'weekly'){
            return Utils::formatArrayValue(Query::table(Task::class)->condition(['uid' => $uid, 'task_id' => $taskId,
                ['reward_time', '>=', strtotime($rewardTime)]])
                ->count()->getResult());
        }
    }

    public static function findDailyListByType($type,$uid,$rewardTime){
        if($type == 'weekly'){

            return  Utils::formatArrayValue(Query::table(Task::class)->condition(['uid' => $uid, ['task_id', '>=', 12000],
                ['task_id', '<=', 13000], ['reward_time', '>=', strtotime($rewardTime)]])->get()->getResult());

        }elseif($type == 'daily'){

            return Utils::formatArrayValue(Query::table(Task::class)->condition(['uid' => $uid, ['task_id', '>=', 11000],
                ['task_id', '<=', 12000], ['reward_time', '=', strtotime($rewardTime)]])->get()->getResult());

        }
        return [];
    }

    /**
     * 获得关于任务的缓存的key
     */
    public static function getKeyByType($type, $uid)
    {
        $today = date("Ymd");
        switch ($type) {
            case "masterFinished" :
                return sprintf(RedisKey::MASTER_FINISH, $uid);
                break;
            case "dailyTask" :
                return sprintf(RedisKey::DAILY_TASK, $uid, $today);
                break;
            case "dailyFinished" :
                return sprintf(RedisKey::DAILY_FINISH, $uid, $today);
                break;
        }
    }

    /**
     * 发放无限生命的奖励
     */
    public static function setUnlimitedLife($id = null)
    {
        $globalInfo = globalInfo();
        $redis = static::getUnlimitedLifeRedis();
        $redisKey = static::getUnlimitedLifeKey($globalInfo->getUuid());
        if ($id !== null) {
            /**
             *读取配置中id对应的有效期
             */
            $unlimited = Config::loadJson('unlimitedLifeBuff');
            if (!isset($unlimited[$id])) {
                return false;
            }
            $unlimitedTime = $unlimited[$id];
            /**
             * 将体力加满
             */
            bean(PropertyService::class)->handleOne(MoneyType::LIVE,25,0,"");
        } else {
            $unlimitedTime = 0;
        }
        /**
         * 设置key值的有效期
         */
        $expireTime = $redis->ttl($redisKey);
        if (in_array($expireTime, [-1, -2])) {
            $expireTime = 0;
        }
        $expireTimePlus = $expireTime + $unlimitedTime;
        $redis->setEx($redisKey, $expireTimePlus, 1);
        return $expireTimePlus;
    }




    /**
     * 获取task配置表中 task配置的第一个id与最后一个id
     */
    private static function getRangeOfTask()
    {
        $taskConfig = Config::loadJson('task')['task'];
        $rang = array();
        //将数组指针指向第一个元素  仅仅为了安全
        reset($taskConfig);
        $rang['lowLimit'] = (int)key($taskConfig);
        //将数组指针指向最后一个元素
        end($taskConfig);
        $rang['upLimit'] = (int)key($taskConfig);
        return $rang;
    }

    /**
     * 对一维数组去重，并重置索引
     */
    private static function uniqueAndReset(&$array)
    {
        $originCount = count($array);
        $array = array_unique($array);
        $afterCount = count($array);
        if ($originCount > $afterCount) {
            $array = array_values($array);
        }
    }

    /**
     * 获取无限生命的key
     */
    public static function getUnlimitedLifeKey($uuid)
    {
        return sprintf(RedisKey::INFINITE_LIFE, $uuid);
    }

    /**
     * 获取无限生命的redis
     */
    public static function getUnlimitedLifeRedis()
    {
        return bean('redis');
    }
}