<?php

namespace App\datalog;

use App\Base\BaseModel;
use App\Constants\OSPlatform;
use App\Models\Dao\CenterUserDao;
use App\Models\Entity\CenterUser;
use App\Tlog\TLog;
use App\Utils\Utils;
use Swoft\App;
use Swoft\Bean\Annotation\Inject;

class DataLog extends BaseModel
{
    //服务器编号
    public $gameSvrId;

    //小区号id（无则报0）
    public $zoneId = 0;

    //微信 1 /手Q 2
    public $areaId;

    public $type = 'business';

    public $actTime;

    public $clientTime;

    public $uuid;

    public $uid;

    public $clientIp;

    public $channel;

    public $appVersion = '';

    public $ClientVersion;

    public $platform = '';

    //ios 0 /android 1
    public $platId;

    public $originAppVersion = null;

    public $deviceId = null;

    public $vGameAppid = null;

    public $vopenid = null;

    public $register_time = null;

    public $registerAppVersion = null;
    

    public function __construct()
    {

        $commonVar = commonVar();
        $platform = $commonVar->getPlatform();
        $appVersion = $commonVar->getAppVersion();
        $deviceId = $commonVar->getDeviceId();

        $uid = $commonVar->getUid();
        if($uid){
            $centerUser = bean(CenterUserDao::class)->findOneByUidFromCache($uid);
        }
        $attrs =  [
            'gameSvrId' => config('serverId') ?? 1001,
            'areaId' => $commonVar->getBindPlatform(),
            'actTime' => time(),
            'clientTime' => request()->post('clientTime'),
            'uuid' => $commonVar->getUuid(),
            'uid' => $commonVar->getUid(),
            'clientIp' => Utils::getRealIp(),
            'channel'=> $commonVar->getChannel(),
            'appVersion'=> Utils::appVersionToInt($appVersion),
            'ClientVersion'=> Utils::appVersionToInt($appVersion),
            'platform'=> $platform,
            'platId'=> OSPlatform::getValueByName($platform),
            'originAppVersion'=> $appVersion,
            'deviceId'=> $deviceId,
            'vGameAppid'=> config('gameAppId', 'guest'),
            'vopenid'=> $commonVar->getOpenId(),
            'register_time'=> isset($centerUser) ? $centerUser->getCreatedAt() : time(),
            'registerAppVersion'=> isset($centerUser) ? $centerUser->getRegisterAppVersion() : '1.0.1'
        ];
        $this->setAttributes($attrs);
    }

    /**
     * 合并客户端传入的log
     */
    public function mergeClientLog($clientLog)
    {
        $this->setAttributes($clientLog);
        return true;
    }

    /**
     * 记录入文件
     * @return bool
     */
    public function send()
    {

        $called_class = explode('\\', get_called_class());
        $category = lcfirst(end($called_class));

        //日志体
        try{
            $body = $this->getAttributes();

            //原始日志，dataLog
            App::info(json_encode($body),[$category]);
            
            //tLog格式的日志
            $logInstance = TLog::getInstance($category, $body);
            if($logInstance){
                $tLog = $logInstance->parse();
                if ($tLog) {
                    bean('tlogLogger')->info($tLog);
                }
            }
        } catch (\Throwable $e) {
           App::error($e->getMessage());
           return false;
        }

        return true;
    }

}