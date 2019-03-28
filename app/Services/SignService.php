<?php

namespace App\Services;

use App\Constants\RedisKey;
use App\Utils\Config;
use App\Utils\ServerTime;
use Swoft\Bean\Annotation\Bean;
use yii\helpers\ArrayHelper;


/**
 * @Bean()
 */
class SignService{
    //周期天数
    const CYCLE_DAYS = 30;

    //补签消耗：20金币
    const REDO_SIGN_COST = 20;


    //签到信息过期时间
    public $sign_info_expire_time = 86400 * self::CYCLE_DAYS;


    /**
     * 签到基础信息
     * @return array
     */
    public function getSignInfo()
    {

        $this->resetSignDays();

        $signInfo['isSigned'] = $this->isSigned();
        //$signInfo['isDailySignBreak'] = false;

        //重置截止时间戳
        $signInfo['resetSeconds'] = $this->getResetSeconds();
        $signInfo['signedDays'] = $this->getSignedDays();
        $signInfo['signOffset'] = $this->getSignOffset();
        $signInfo['redoCost'] = self::REDO_SIGN_COST;

        return $signInfo;
    }

    /**
     * 签到
     * @param null $time
     * @return mixed
     */
    public function sign($time = null){
        if($time == null){
            $time = ServerTime::getTestTime();
        }
        $redis = bean('redis');
        $globalInfo = globalInfo();
        $keyExisted = $redis->has(self::getSignKey($globalInfo->getUid()));
        $ret = $redis->ZADD(self::getSignKey($globalInfo->getUid()), $time, date('Ymd', $time));

        if(!$keyExisted){
            $end_day = date('Ymd', strtotime("+" . self::CYCLE_DAYS . " days"));
            $redis->EXPIREAT(self::getSignKey($globalInfo->getUid()), strtotime($end_day));
        }

        return $ret;
    }

    /**
     * 补签
     */
    public function redoSign(){
        $redis = bean('redis');
        $lastDay = $this->getLastSignDay();
        if(!$lastDay){
            return false;
        }
        $globalInfo = globalInfo();
        $nextDateTime = strtotime($lastDay) + 86400;
        return $redis->ZADD(self::getSignKey($globalInfo->getUid()), $nextDateTime, date('Ymd', $nextDateTime));
    }

    /**
     * 周期结束时间
     */
    public function getResetSeconds(){
        $redis = bean('redis');
        $globalInfo = globalInfo();
        return $redis->TTL(self::getSignKey($globalInfo->getUid()));
    }

    /**
     * @param null $time
     * 是否已经签到
     * @return mixed
     */
    public function isSigned($time = null){
        if($time == null){
            $time = ServerTime::getTestTime();
        }
        $redis = bean('redis');
        $globalInfo = globalInfo();
        $ret =  $redis->ZSCORE(self::getSignKey($globalInfo->getUid()), date('Ymd', $time));
        return $ret ? true : false;
    }


    /**
     * 已经签到次数
     * @return mixed
     */
    public function getSignedDays(){
        $redis = bean('redis');
        $globalInfo = globalInfo();
        return $redis->ZCARD(self::getSignKey($globalInfo->getUid()));
    }

    /**
     * 返回当前周期的第几天
     */
    public function getSignOffset(){
        $redis = bean('redis');
        $globalInfo = globalInfo();
        $firsDay = $redis->ZRANGE(self::getSignKey($globalInfo->getUid()), 0, 0);
        if(!isset($firsDay[0])){
            return 1;
        }
        return floor((ServerTime::getTestTime() - strtotime($firsDay[0])) / (3600 * 24)) + 1;
    }

    public function getLastSignDay(){

        $ret = '';
        $redis = bean('redis');
        $globalInfo = globalInfo();
        $allDays = $redis->ZRANGE(self::getSignKey($globalInfo->getUid()), 0, -1);

        if(count($allDays) == 1){
            return $allDays[0];
        }

        for($i = 0; $i < count($allDays) - 1; $i++){
            $diffTime = strtotime($allDays[$i + 1]) - strtotime($allDays[$i]);
            if($diffTime == 86400){
                continue;
            }else{
                $ret = $allDays[$i];
                break;
            }
        }

        return $ret;
    }


    public function getRewardConfigFromCache()
    {
        $redis = bean('redis');
        $globalInfo = globalInfo();
        $ret = $redis->get(self::getRewardConfigKey($globalInfo->getUid()));
        if (!$ret) {
            return [];
        }
        return json_decode($ret, true);
    }

    public function cacheRewardConfig($config)
    {
        $redis = bean('redis');
        $globalInfo = globalInfo();
        return $redis->setEx(self::getRewardConfigKey($globalInfo->getUid()),
            $this->sign_info_expire_time, json_encode($config));
    }

    /**
     * 从缓存取当前配置，不存在则从配置列表中随机
     */
    public function getRewardConfig()
    {

        $reward_config = $this->getRewardConfigFromCache();
        if (!$reward_config) {
            return $this->resetRewardConfig(false);
        }

        return $reward_config;
    }

    /**
     * 符合条件则情况签到记录，重置奖励配置
     */
    public function resetSignDays()
    {
        $offset = $this->getSignOffset();
        if (($offset > self::CYCLE_DAYS)) {
            $this->flushSignInfo();
            $this->resetRewardConfig();
        }
        return true;
    }

    public function flushSignInfo()
    {
        $redis = bean('redis');
        $globalInfo = globalInfo();
        return $redis->DEL(self::getSignKey($globalInfo->getUid()));
    }

    /**
     * 重置配置
     */
    public function resetRewardConfig($diff = true)
    {
        $ret = [];
        $total_config_arr = Config::loadJson('dailySignReward');
        if ($total_config_arr) {
            if($diff){
                $prev_config = $this->getRewardConfigFromCache();
                ArrayHelper::removeValue($total_config_arr, $prev_config);
            }

            $rand_key = array_rand($total_config_arr);
            $ret = $total_config_arr[$rand_key];

            //redis缓存每期奖励配置
            $this->cacheRewardConfig($ret);
        }
        return $ret;
    }

    /**
     * 获取签到的key
     */
    public static function getSignKey($uid)
    {
        return sprintf(RedisKey::SIGN_IN, $uid);
    }

    public static function getRewardConfigKey($uuid)
    {
        return sprintf(RedisKey::USER_SIGN_CONFIG, $uuid);
    }
}