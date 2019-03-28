<?php

namespace App\Services;

use App\Constants\RedisKey;
use App\Constants\TaskProgressType;
use App\Utils\Config;
use App\Utils\ServerTime;
use yii\helpers\ArrayHelper;



abstract class TaskProgressService
{

    protected $redis = null;

    protected $_testTime = null;

    public function __construct()
    {
        if($this->_testTime == null){
            $this->_testTime = ServerTime::getTestTime();
        }

        $this->redis = bean('redis');
    }

    public static function factory($type = 0)
    {
        $className = TaskProgressType::valueToCamelStr($type);
        $class = 'App\\Services\\' . $className . 'ProgressService';
        if (class_exists($class)) {
            $instance = new $class;
        }else{
            throw new \Exception("invalid task progress type");
        }

        return $instance;
    }

    /**
     * 更新任务目标进度
     */
    public function updateProgress($targetId, $incNum, $isReset = false){
        $keyName = $this->getProgressKey();

        $exists = false;
        if($this->redis->EXISTS($keyName)){
            $exists = true;
        }

        $isReset ? $this->redis->HSET($keyName, $targetId, $incNum) : $this->redis->HINCRBY($keyName, $targetId, $incNum);

        if(!$exists && $this->expireAt()){
            $this->redis->EXPIREAT($keyName, $this->expireAt());
        }
    }

    /**
     * 获取所有目标的进度
     * @return mixed
     */
    public function getProgress(){
        return $this->redis->HGETALL($this->getProgressKey());
    }

    /**
     * 获取单个目标的进度
     * @param $targetId
     * @return mixed
     */
    public function getProgressByTargetId($targetId){
        return $this->redis->HGET($this->getProgressKey(), $targetId);
    }

    /**
     * 获取额外信息
     * @return mixed
     */
    public function getExtra(){
        $rows = $this->redis->HGETALL($this->getExtraKey());

        if($rows){
            foreach ($rows as &$row){
                $row = json_decode($row,true);
            }
        }

        return $rows;
    }


    /**
     * 更新额外信息
     */
    public function updateExtra($targetId, $extra){
        return $this->redis->HSET($this->getExtraKey(), $targetId, $extra);
    }

    public function getRewardConfigFromCache()
    {
        $ret = $this->redis->get($this->getRewardConfigKey());
        if (!$ret) {
            return [];
        }
        return json_decode($ret, true);
    }

    public function getPrevRewardConfigFromCache()
    {
        $ret = $this->redis->get($this->getPrevRewardConfigKey());
        if (!$ret) {
            return [];
        }
        return json_decode($ret, true);
    }

    public function cacheRewardConfig($config)
    {
        $this->redis->set($this->getRewardConfigKey(), json_encode($config));
        if($this->expireAt()){
            $this->redis->EXPIREAT($this->getRewardConfigKey(), $this->expireAt());
        }
    }

    /**
     * 从缓存取当前配置，不存在则从配置列表中随机
     */
    public function getRewardConfig()
    {

        $reward_config = $this->getRewardConfigFromCache();
        if (!$reward_config) {
            $reward_config =  $this->resetRewardConfig();
        }

        $taskProgress = $this->getProgress();

        foreach ($reward_config as &$item) {
            $targetId = $item['target_type'];
            if(isset($taskProgress[$targetId])){
                $item['current_num'] = (int)$taskProgress[$targetId];
            }else{
                $item['current_num'] = 0;
            }

            $item['is_rewarded'] = isset($item['is_rewarded']) ? $item['is_rewarded'] : false;
        }

        return $reward_config;
    }

    /**
     * 重置配置
     */
    public function resetRewardConfig($diff = true)
    {
        $ret = [];
        $configName = $this->getConfigName();
        $total_config_arr = Config::loadJson($configName);
        if ($total_config_arr) {
            if($diff){
                $prev_config = $this->getPrevRewardConfigFromCache();
                foreach ($prev_config as &$config){
                    if(isset($config['is_rewarded'])){
                        unset($config['is_rewarded']);
                    }
                }
                ArrayHelper::removeValue($total_config_arr, $prev_config);
            }

            $rand_key = array_rand($total_config_arr);
            $ret = $total_config_arr[$rand_key];

            //redis缓存每期奖励配置
            $this->cacheRewardConfig($ret);
        }
        return $ret;
    }

    public function isRewarded($taskId){
        $reward_config = $this->getRewardConfigFromCache();
        if(!$reward_config){
            return false;
        }

        if(!isset($reward_config[$taskId])){
            return false;
        }

        if(!isset($reward_config[$taskId]['is_rewarded'])){
            return false;
        }

        return $reward_config[$taskId]['is_rewarded'];
    }

    public function getExtraKey(){
        return sprintf(RedisKey::TARGET_EXTRA, globalInfo()->getUid());
    }

    abstract  protected function getProgressKey();

    /**
     * @return mixed
     */
    abstract protected function getRewardConfigKey();

    abstract protected function getPrevRewardConfigKey();

    abstract  protected function getConfigName();

    /**
     * redis key的过期时间
     * @return mixed
     */
    abstract protected function expireAt();



}
