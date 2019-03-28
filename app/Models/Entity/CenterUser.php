<?php

namespace App\Models\Entity;

use App\Constants\RedisKey;
use Swoft\App;
use Swoft\Db\Model;
use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Required;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Db\Types;

/**
 * shard_key “thirdId”
 * @Entity(instance="global")
 * @Table(name="center_user")
 * @uses      CenterUser
 */
class CenterUser extends Model
{

    const TOKEN_SECRET_CACHE_TIME = 604800;//七天

    /**
     * @var int $uid 同uid
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var int $customerId rovio customerId
     * @Column(name="customerId", type="bigint", default=0)
     */
    private $customerId;

    /**
     * @var string $uuid uuid
     * @Column(name="uuid", type="string", length=100)
     * @Required()
     */
    private $uuid;

    /**
     * @var int $thirdBind 第三方绑定类型1-qq 2-wx 3-guest
     * @Column(name="thirdBind", type="tinyint", default=3)
     */
    private $thirdBind;

    /**
     * @var string $thirdId 第三方id wx_ios_xxxxx
     * @Column(name="thirdId", type="string", length=60, default="")
     */
    private $thirdId;

    /**
     * @var string $platform 平台 ios 、android
     * @Column(name="platform", type="string", length=10)
     * @Required()
     */
    private $platform;

    /**
     * @var string $secretKey 密钥
     * @Column(name="secretKey", type="string", length=32, default="")
     */
    private $secretKey;

    /**
     * @var string $zone 分配的zone
     * @Column(name="zone", type="string", length=20)
     * @Required()
     */
    private $zone;

    /**
     * @var string $channel 注册渠道
     * @Column(name="channel", type="string", length=30, default="unknown")
     */
    private $channel;

    /**
     * @var string $deviceId
     * @Column(name="deviceId", type="string", length=100, default="")
     */
    private $deviceId;

    /**
     * @var string $registerAppVersion 注册版本号
     * @Column(name="registerAppVersion", type="string", length=50, default="100")
     */
    private $registerAppVersion;

    /**
     * @var int $createdAt
     * @Column(name="created_at", type="integer")
     * @Required()
     */
    private $createdAt;

    /**
     * @var int $updatedAt
     * @Column(name="updated_at", type="integer")
     * @Required()
     */
    private $updatedAt;

    /**
     * 同uid
     * @param int $value
     * @return $this
     */
    public function setUid(int $value)
    {
        $this->uid = $value;

        return $this;
    }

    /**
     * rovio customerId
     * @param int $value
     * @return $this
     */
    public function setCustomerId(int $value): self
    {
        $this->customerId = $value;

        return $this;
    }

    /**
     * uuid
     * @param string $value
     * @return $this
     */
    public function setUuid(string $value): self
    {
        $this->uuid = $value;

        return $this;
    }

    /**
     * 第三方绑定类型1-qq 2-wx 3-guest
     * @param int $value
     * @return $this
     */
    public function setThirdBind(int $value): self
    {
        $this->thirdBind = $value;

        return $this;
    }

    /**
     * 第三方id wx_ios_xxxxx
     * @param string $value
     * @return $this
     */
    public function setThirdId(string $value): self
    {
        $this->thirdId = $value;

        return $this;
    }

    /**
     * 平台 ios 、android
     * @param string $value
     * @return $this
     */
    public function setPlatform(string $value): self
    {
        $this->platform = $value;

        return $this;
    }

    /**
     * 密钥
     * @param string $value
     * @return $this
     */
    public function setSecretKey(string $value): self
    {
        $this->secretKey = $value;

        return $this;
    }

    /**
     * 分配的zone
     * @param string $value
     * @return $this
     */
    public function setZone(string $value): self
    {
        $this->zone = $value;

        return $this;
    }

    /**
     * 注册渠道
     * @param string $value
     * @return $this
     */
    public function setChannel(string $value): self
    {
        $this->channel = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDeviceId(string $value): self
    {
        $this->deviceId = $value;

        return $this;
    }

    /**
     * 注册版本号
     * @param string $value
     * @return $this
     */
    public function setRegisterAppVersion(string $value): self
    {
        $this->registerAppVersion = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setCreatedAt(int $value): self
    {
        $this->createdAt = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setUpdatedAt(int $value): self
    {
        $this->updatedAt = $value;

        return $this;
    }

    /**
     * 同uid
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * rovio customerId
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * uuid
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * 第三方绑定类型1-qq 2-wx 3-guest
     * @return mixed
     */
    public function getThirdBind()
    {
        return $this->thirdBind;
    }

    /**
     * 第三方id wx_ios_xxxxx
     * @return string
     */
    public function getThirdId()
    {
        return $this->thirdId;
    }

    /**
     * 平台 ios 、android
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * 密钥
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * 分配的zone
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * 注册渠道
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * 注册版本号
     * @return mixed
     */
    public function getRegisterAppVersion()
    {
        return $this->registerAppVersion;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
}
