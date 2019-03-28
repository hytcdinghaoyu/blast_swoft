<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2019/1/11
 * Time: 上午11:04
 */

namespace App\Utils;


class CommonVar
{

    protected $platform;
    protected $bindPlatform;
    protected $openId;
    protected $appVersion;
    protected $deviceId;
    protected $channel;
    protected $configVersion;
    protected $uid;
    protected $uuid;

    public function withAttributes(array $attrs)
    {
        $commonVar = clone $this;
        foreach ($attrs as $key => $value) {
            if (property_exists($commonVar, $key)) {
                $commonVar->$key = $value;
            } else {
                throw new \InvalidArgumentException("Unexpected commonVar key $key!");
            }
        }
        return $commonVar;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function getBindPlatform()
    {
        return $this->bindPlatform;
    }

    public function getOpenId()
    {
        return $this->openId;
    }

    public function getAppVersion()
    {
        return $this->appVersion;
    }

    public function getDeviceId()
    {
        return $this->deviceId;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getConfigVersion()
    {
        return $this->configVersion;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}