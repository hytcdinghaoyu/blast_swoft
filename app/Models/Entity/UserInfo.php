<?php
namespace App\Models\Entity;

use Swoft\Db\Model;
use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Required;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Db\Types;

/**
 * shard_key "uid"

 * @Entity(instance="api")
 * @Table(name="user_info")
 * @uses      UserInfo
 */
class UserInfo extends Model
{
    /**
     * @var int $uid 用户ID，用来识别用户数据
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var int $silverCoin 银币
     * @Column(name="silver_coin", type="integer", default=0)
     */
    private $silverCoin;

    /**
     * @var int $goldCoin 金币
     * @Column(name="gold_coin", type="integer", default=0)
     */
    private $goldCoin;

    /**
     * @var int $lives 活力值
     * @Column(name="lives", type="smallint", default=0)
     */
    private $lives;

    /**
     * @var int $last 上次活力值的更新时间
     * @Column(name="last", type="bigint", default=0)
     */
    private $last;

    /**
     * @var int $newLevel 关卡
     * @Column(name="new_level", type="smallint", default=1)
     */
    private $newLevel;

    /**
     * @var int $stars 总星星数量
     * @Column(name="stars", type="integer", default=0)
     */
    private $stars;

    /**
     * @var int $remainStars 剩余星星数
     * @Column(name="remainStars", type="integer", default=0)
     */
    private $remainStars;

    /**
     * @var string $avatar 头像图标
     * @Column(name="avatar", type="string", length=255, default="1")
     */
    private $avatar;

    /**
     * @var string $username 用户昵称
     * @Column(name="username", type="string", length=50, default="")
     */
    private $username;

    /**
     * @var int $population 人口数
     * @Column(name="population", type="integer", default=0)
     */
    private $population;

    /**
     * @var int $prosperity 繁荣度
     * @Column(name="prosperity", type="integer", default=0)
     */
    private $prosperity;

    /**
     * @var int $tencentVip 0:非会员 1:普通会员 2:超级会员
     * @Column(name="tencentVip", type="tinyint", default=0)
     */
    private $tencentVip;

    /**
     * @var int $saveAmt 历史充值金额（钻石数）
     * @Column(name="save_amt", type="integer", default=0)
     */
    private $saveAmt;

    /**
     * @var int $status 0:下线玩家 1:在线正常角色 2:暂时封停角色 3:为永久封停角色
     * @Column(name="status", type="tinyint", default=1)
     */
    private $status;

    /**
     * @var int $lastLoginAt 最后登录时间
     * @Column(name="last_login_at", type="integer", default=0)
     */
    private $lastLoginAt;

    /**
     * @var int $createdAt 
     * @Column(name="created_at", type="integer")
     */
    private $createdAt;

    /**
     * @var int $updatedAt 
     * @Column(name="updated_at", type="integer")
     */
    private $updatedAt;

    /**
     * 用户ID，用来识别用户数据
     * @param int $value
     * @return $this
     */
    public function setUid(int $value)
    {
        $this->uid = $value;

        return $this;
    }

    /**
     * 银币
     * @param int $value
     * @return $this
     */
    public function setSilverCoin(int $value): self
    {
        $this->silverCoin = $value;

        return $this;
    }

    /**
     * 金币
     * @param int $value
     * @return $this
     */
    public function setGoldCoin(int $value): self
    {
        $this->goldCoin = $value;

        return $this;
    }

    /**
     * 活力值
     * @param int $value
     * @return $this
     */
    public function setLives(int $value): self
    {
        $this->lives = $value;

        return $this;
    }

    /**
     * 上次活力值的更新时间
     * @param int $value
     * @return $this
     */
    public function setLast(int $value): self
    {
        $this->last = $value;

        return $this;
    }

    /**
     * 关卡
     * @param int $value
     * @return $this
     */
    public function setNewLevel(int $value): self
    {
        $this->newLevel = $value;

        return $this;
    }

    /**
     * 总星星数量
     * @param int $value
     * @return $this
     */
    public function setStars(int $value): self
    {
        $this->stars = $value;

        return $this;
    }

    /**
     * 剩余星星数
     * @param int $value
     * @return $this
     */
    public function setRemainStars(int $value): self
    {
        $this->remainStars = $value;

        return $this;
    }

    /**
     * 头像图标
     * @param string $value
     * @return $this
     */
    public function setAvatar(string $value): self
    {
        $this->avatar = $value;

        return $this;
    }

    /**
     * 用户昵称
     * @param string $value
     * @return $this
     */
    public function setUsername(string $value): self
    {
        $this->username = $value;

        return $this;
    }

    /**
     * 人口数
     * @param int $value
     * @return $this
     */
    public function setPopulation(int $value): self
    {
        $this->population = $value;

        return $this;
    }

    /**
     * 繁荣度
     * @param int $value
     * @return $this
     */
    public function setProsperity(int $value): self
    {
        $this->prosperity = $value;

        return $this;
    }

    /**
     * 0:非会员 1:普通会员 2:超级会员
     * @param int $value
     * @return $this
     */
    public function setTencentVip(int $value): self
    {
        $this->tencentVip = $value;

        return $this;
    }

    /**
     * 历史充值金额（钻石数）
     * @param int $value
     * @return $this
     */
    public function setSaveAmt(int $value): self
    {
        $this->saveAmt = $value;

        return $this;
    }

    /**
     * 0:下线玩家 1:在线正常角色 2:暂时封停角色 3:为永久封停角色
     * @param int $value
     * @return $this
     */
    public function setStatus(int $value): self
    {
        $this->status = $value;

        return $this;
    }

    /**
     * 最后登录时间
     * @param int $value
     * @return $this
     */
    public function setLastLoginAt(int $value): self
    {
        $this->lastLoginAt = $value;

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
     * 用户ID，用来识别用户数据
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * 银币
     * @return int
     */
    public function getSilverCoin()
    {
        return $this->silverCoin;
    }

    /**
     * 金币
     * @return int
     */
    public function getGoldCoin()
    {
        return $this->goldCoin;
    }

    /**
     * 活力值
     * @return int
     */
    public function getLives()
    {
        return $this->lives;
    }

    /**
     * 上次活力值的更新时间
     * @return int
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * 关卡
     * @return mixed
     */
    public function getNewLevel()
    {
        return $this->newLevel;
    }

    /**
     * 总星星数量
     * @return int
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * 剩余星星数
     * @return int
     */
    public function getRemainStars()
    {
        return $this->remainStars;
    }

    /**
     * 头像图标
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * 用户昵称
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * 人口数
     * @return int
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * 繁荣度
     * @return int
     */
    public function getProsperity()
    {
        return $this->prosperity;
    }

    /**
     * 0:非会员 1:普通会员 2:超级会员
     * @return int
     */
    public function getTencentVip()
    {
        return $this->tencentVip;
    }

    /**
     * 历史充值金额（钻石数）
     * @return int
     */
    public function getSaveAmt()
    {
        return $this->saveAmt;
    }

    /**
     * 0:下线玩家 1:在线正常角色 2:暂时封停角色 3:为永久封停角色
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 最后登录时间
     * @return int
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
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
