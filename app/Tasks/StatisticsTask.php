<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Tasks;

use App\Constants\RedisKey;
use App\Constants\ZoneAreaId;
use App\Models\Dao\UserInfoDao;
use App\Tlog\LogoutLog;
use Swoft\App;
use Swoft\Bean\Annotation\Inject;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Core\Coroutine;
use Swoft\Core\RequestContext;
use Swoft\Task\Bean\Annotation\Scheduled;
use Swoft\Task\Bean\Annotation\Task;
use yii\helpers\ArrayHelper;
use App\Models\Entity\TbFnxnbbOnlinecnt;

/**
 * Sync task
 *
 * @Task("statistics")
 */
class StatisticsTask
{

    //玩家下线时间间隔
    const PLAYER_LOGOUT_INTERVAL = 600;

    /**
     * 统计在线时长并输出登出日志
     * @Scheduled(cron="0 * * * * *")
     *
     */
    public function onlineCntTask()
    {
       
        if(!config('onlineCntEnabled')){
            return;
        }
    
        ConsoleUtil::log('Start ' . __FUNCTION__ . " pid : " . getmypid());
        $redis = bean('redis');
        $nowTime = time();
        $uid_all = [];
        $gameServerId = config('serverId');
        $zoneAreaId = config('zoneId');

        $online_set_key = RedisKey::ONLINE_CNT;
        $model = new TbFnxnbbOnlinecnt();
        $model->setTimekey($nowTime);
        $model->setGsid($gameServerId);
        $model->setZoneareaid($zoneAreaId);
        $onlineCount = $redis->ZCOUNT($online_set_key, $nowTime - self::PLAYER_LOGOUT_INTERVAL, $nowTime);

        if(in_array($zoneAreaId, [ZoneAreaId::QQ_IOS, ZoneAreaId::WX_IOS])){
            $model->setOnlinecntios($onlineCount);
        }elseif (in_array($zoneAreaId, [ZoneAreaId::QQ_ANDROID, ZoneAreaId::WX_ANDROID])){
            $model->setOnlinecntandroid($onlineCount);
        }else{
            $model->setOnlinecntandroid($onlineCount);
        }

        $gameAppId = config('gameAppId','78yiuhhiuh');
        $model->setGameappid($gameAppId);


        $model->setReporttime(time());
        $model->save()->getResult();
        ConsoleUtil::log("zoneAreaId:{$model->getZoneareaid()}, gameServerId:{$gameServerId},ios在线人数:{$model->getOnlinecntios()},android在线人数:{$model->getOnlinecntandroid()}");


        //处理登出
        $uid_arr = $redis->ZRANGEBYSCORE($online_set_key, 0, $nowTime - self::PLAYER_LOGOUT_INTERVAL, array('withscores' => true));
        if (!empty($uid_arr)) {
            $uid_all = empty($uid_all) ? $uid_arr : ArrayHelper::merge($uid_all, $uid_arr);
            $redis->ZREMRANGEBYSCORE($online_set_key, 0, $nowTime - self::PLAYER_LOGOUT_INTERVAL);
        }
        if ($uid_all) {
            $this->processLogout($uid_all);
        }
    
        ConsoleUtil::log("过去10分钟logout总人数:" . count($uid_all));
        ConsoleUtil::log('End ' . __FUNCTION__);
    }

    private function processLogout($uidScores)
    {
        if (!empty($uidScores)) {
            $uids = array_keys($uidScores);
            $redis = bean('redis');
            $playerInfoList = bean(UserInfoDao::class)->getAllByUidArr($uids);
            $playerLastLoginList = $redis->hMGET(RedisKey::USER_LAST_LOGIN, $uids);
            foreach ($playerInfoList as $item) {
                $uid = $item['uid'];
                if ($playerLastLoginList[$uid]) {

                    $logoutInfo = json_decode($playerLastLoginList[$uid], true);

                    //计算在线时长
                    $logoutTime = isset($uidScores[$uid]) ? $uidScores[$uid] : time();
                    $logoutInfo['OnlineTime'] = $logoutTime - $logoutInfo['actTime'];

                    if ($logoutInfo['OnlineTime'] <= 0) {
                        continue;
                    }

                    //生成log
                    $logoutLog = new LogoutLog();
                    $logoutLog->setAttributes($logoutInfo);
                    $logoutLog->Level = $item['new_level'] - 1;
                    $log = $logoutLog->parse();
                    bean('tlogLogger')->info($log);
                    
                    ConsoleUtil::log("uid：{$uid} 登出.");

                } else {
                    App::error("last login info lost while process logout, uid:{$uid}");
                }

            }
        }
    }
}
