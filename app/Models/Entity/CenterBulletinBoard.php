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
 * @Entity(instance="global")
 * @Table(name="center_bulletin_board")
 * @uses      CenterBulletinBoard
 */
class CenterBulletinBoard extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string $name 公告名称
     * @Column(name="name", type="string", length=255)
     * @Required()
     */
    private $name;

    /**
     * @var int $type 类型 默认为0
     * @Column(name="type", type="tinyint", default=0)
     */
    private $type;

    /**
     * @var int $rewardId 奖励id 0，无奖励
     * @Column(name="reward_id", type="integer", default=0)
     */
    private $rewardId;

    /**
     * @var int $activityId 跳转活动id 0,不跳转
     * @Column(name="activity_id", type="integer", default=0)
     */
    private $activityId;

    /**
     * @var int $isActived 是否被激活 0 没有 1 激活
     * @Column(name="is_actived", type="tinyint", default=0)
     */
    private $isActived;

    /**
     * @var int $isTesting 是否是测试 0 不是 ，1 是
     * @Column(name="is_testing", type="tinyint")
     * @Required()
     */
    private $isTesting;

    /**
     * @var int $isDeleted 是否删除 0未删除 1已删除
     * @Column(name="is_deleted", type="tinyint", default=0)
     */
    private $isDeleted;

    /**
     * @var int $isForced 是否强制弹出
     * @Column(name="is_forced", type="tinyint", default=0)
     */
    private $isForced;

    /**
     * @var string $extra 备用字段，根据不同的通知类型可能有不同的作用,比如倒计时
     * @Column(name="extra", type="string", length=255, default="")
     */
    private $extra;

    /**
     * @var int $sort 排序
     * @Column(name="sort", type="tinyint", default=0)
     */
    private $sort;

    /**
     * @var int $startAt 开始时间戳
     * @Column(name="start_at", type="integer", default=0)
     */
    private $startAt;

    /**
     * @var int $endAt 结束时间戳
     * @Column(name="end_at", type="integer", default=0)
     */
    private $endAt;

    /**
     * @var string $version 允许的版本号
     * @Column(name="version", type="string", length=255, default="[]")
     */
    private $version;

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
     * @param int $value
     * @return $this
     */
    public function setId(int $value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * 公告名称
     * @param string $value
     * @return $this
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * 类型 默认为0
     * @param int $value
     * @return $this
     */
    public function setType(int $value): self
    {
        $this->type = $value;

        return $this;
    }

    /**
     * 奖励id 0，无奖励
     * @param int $value
     * @return $this
     */
    public function setRewardId(int $value): self
    {
        $this->rewardId = $value;

        return $this;
    }

    /**
     * 跳转活动id 0,不跳转
     * @param int $value
     * @return $this
     */
    public function setActivityId(int $value): self
    {
        $this->activityId = $value;

        return $this;
    }

    /**
     * 是否被激活 0 没有 1 激活
     * @param int $value
     * @return $this
     */
    public function setIsActived(int $value): self
    {
        $this->isActived = $value;

        return $this;
    }

    /**
     * 是否是测试 0 不是 ，1 是
     * @param int $value
     * @return $this
     */
    public function setIsTesting(int $value): self
    {
        $this->isTesting = $value;

        return $this;
    }

    /**
     * 是否删除 0未删除 1已删除
     * @param int $value
     * @return $this
     */
    public function setIsDeleted(int $value): self
    {
        $this->isDeleted = $value;

        return $this;
    }

    /**
     * 是否强制弹出
     * @param int $value
     * @return $this
     */
    public function setIsForced(int $value): self
    {
        $this->isForced = $value;

        return $this;
    }

    /**
     * 备用字段，根据不同的通知类型可能有不同的作用,比如倒计时
     * @param string $value
     * @return $this
     */
    public function setExtra(string $value): self
    {
        $this->extra = $value;

        return $this;
    }

    /**
     * 排序
     * @param int $value
     * @return $this
     */
    public function setSort(int $value): self
    {
        $this->sort = $value;

        return $this;
    }

    /**
     * 开始时间戳
     * @param int $value
     * @return $this
     */
    public function setStartAt(int $value): self
    {
        $this->startAt = $value;

        return $this;
    }

    /**
     * 结束时间戳
     * @param int $value
     * @return $this
     */
    public function setEndAt(int $value): self
    {
        $this->endAt = $value;

        return $this;
    }

    /**
     * 允许的版本号
     * @param string $value
     * @return $this
     */
    public function setVersion(string $value): self
    {
        $this->version = $value;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 公告名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 类型 默认为0
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 奖励id 0，无奖励
     * @return int
     */
    public function getRewardId()
    {
        return $this->rewardId;
    }

    /**
     * 跳转活动id 0,不跳转
     * @return int
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * 是否被激活 0 没有 1 激活
     * @return int
     */
    public function getIsActived()
    {
        return $this->isActived;
    }

    /**
     * 是否是测试 0 不是 ，1 是
     * @return int
     */
    public function getIsTesting()
    {
        return $this->isTesting;
    }

    /**
     * 是否删除 0未删除 1已删除
     * @return int
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * 是否强制弹出
     * @return int
     */
    public function getIsForced()
    {
        return $this->isForced;
    }

    /**
     * 备用字段，根据不同的通知类型可能有不同的作用,比如倒计时
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * 排序
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * 开始时间戳
     * @return int
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * 结束时间戳
     * @return int
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * 允许的版本号
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
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
