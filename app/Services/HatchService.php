<?php

namespace App\Services;

use App\Constants\ActivityType;
use App\Constants\RedisKey;
use App\Models\Dao\CenterActivityDao;
use App\Models\Dao\UserInfoDao;
use App\Utils\Config;
use App\Utils\ServerTime;
use Swoft\Bean\Annotation\Bean;


/**
 * @Bean()
 */
class HatchService
{

    const  REWARD_NOT_GOTTEN = 0;

    const  REWARD_GOTTEN = 1;

    const  EXPIRE_TIME = 3456000;//过期时间，大于赛季时间


    /**
     * 登录以后user/init调用
     */
    public function getHatchAllInfo(){
        $hatchRestTime = $this->getRestTime();
        $hatchRewardList = $this->getRewardList();
        $userCurProgress = $this->getCurProgress();
        $curHatchInfo = $this->getCurHatchInfo();
        $config_level = 0;
        if (empty($curHatchInfo)){//关卡没有开启，奖励列表，配置关卡清空
            $hatchRewardList = [];
        }else{
            $extra = json_decode($curHatchInfo['extra'],true);
            if (is_array($extra)){
                $config_level = $extra['config_level'];
            }
        }
        return [
            'configLevel'=>(int)$config_level,
            'hatchRestTime'=>(int)$hatchRestTime,
            'rewardConfig'=>[
                'rewardList'=>$hatchRewardList,
                'userCurProgress'=>(int)$userCurProgress
            ]
        ];
    }

    /**
     * 获取玩家当前进度
     * @return int
     */
    public function getCurProgress(){
        $globalInfo = globalInfo();
        $curHatchInfo = $this->getCurHatchInfo();
        if (empty($curHatchInfo)) {
            return 0;
        }
        $progressKey = $this->getProgressKey($curHatchInfo['name'], $globalInfo->getUuid());
        $curProcess = bean('redis')->get($progressKey);
        if (isset($curProcess)){
            return $curProcess;
        }
        return 0;
    }

    /**
     *检查当前时间是否能开启孵化
     */
    public function checkHatchTime()
    {
        $curHatchInfo = $this->getCurHatchInfo();
        if (empty($curHatchInfo)) {
            return false;
        }
        return true;
    }
    /**
     *检查用户是否能开启孵化
     */
    public function checkUser()
    {

        $curHatchInfo = $this->getCurHatchInfo();
        if (empty($curHatchInfo)) {
            return false;
        }
        $globalInfo = globalInfo();
        $time = ServerTime::getTestTime();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        if ($userInfo->getNewLevel() > $curHatchInfo['config_level'] && $time < $curHatchInfo['end_at'] && $time > $curHatchInfo['start_at']) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前赛季剩下的时间
     */
    public function getRestTime()
    {
        $centerHatchEnd = $this->getCurHatchInfo();
        if (empty($centerHatchEnd)) {
            return 0;
        }
        $timeTmp = ServerTime::getTestTime();
        $time = $centerHatchEnd['end_at'] - $timeTmp;
        return $time;
    }


    /**
     * 找到当前赛季的信息
     */
    public function getCurHatchInfo() {
        $now = ServerTime::getTestTime();
        $seasons = $this->getSeasonList();
        foreach ($seasons as $season){
            if($season['start_at'] < $now && $season['end_at'] > $now){
                return $season;
            }
        }
    }

    public function getSeasonList(){
        $seasons = CenterActivityDao::getSeasonListByType(ActivityType::HATCH);
        return $seasons;
    }


    /**
     * 用户的奖励列表领取情况
     * 这个进度是用户完成的关卡-赛季配置的关卡
     * @param $progress
     * @return bool
     */
    public function getRewardList()
    {
        $rewardList = Config::loadJson('hatchReward');
        return $rewardList;
    }



    /**
     * 领取奖励
     * 这个进度是用户完成的关卡减去赛季配置的关卡
     * @param $progress
     * @return bool
     */
    public function getReward($progress)
    {
        $globalInfo = globalInfo();
        $curHatchInfo = $this->getCurHatchInfo();
        if (empty($curHatchInfo)) {
            return false;
        }

        //查看用户有没有领过
        $rewardKey = $this->getRewardKey($curHatchInfo['name'], $globalInfo->getUuid());
        $isGotten = bean('redis')->hGet($rewardKey, $progress);
        $rewardProgressArr = array_keys(Config::loadJson('hatchReward'));
        if (empty($isGotten)) {//防止客户端乱输，必须是未领取过的progress才能领
            if(in_array($progress,$rewardProgressArr)){//在[2,7,15]之内才发奖励
                bean('redis')->hSet($rewardKey, $progress, self::REWARD_GOTTEN);
                bean('redis')->expire($rewardKey,self::EXPIRE_TIME);
            }
            return true;
        }
        return false;

    }

    /**
     * 用户关卡进度更新
     * 这个进度是用户完成的关卡减去赛季配置的关卡
     * @param $progress
     * @return bool
     */
    public function updateUserProgress()
    {
        //更新progress
        $globalInfo = globalInfo();
        $curHatchInfo = $this->getCurHatchInfo();
        if (empty($curHatchInfo)) {
            return false;
        }
        $progressKey = $this->getProgressKey($curHatchInfo['name'], $globalInfo->getUuid());
        bean('redis')->incr($progressKey);
        bean('redis')->expire($progressKey,self::EXPIRE_TIME);
        return true;

    }

    public function getProgressKey($seasonName, $uuid)
    {
        return sprintf(RedisKey::HATCH_USER_PROGRESS, $seasonName, $uuid);
    }

    public function getRewardKey($seasonName, $uuid)
    {
        return sprintf(RedisKey::HATCH_USER_REWARD, $seasonName, $uuid);
    }
}