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
 * 运营活动配置表

 * @Entity(instance="global")
 * @Table(name="center_activity")
 * @uses      CenterActivity
 */
class CenterActivity extends Model
{
    /**
     * @var int $id 活动ID
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int $type 活动类型ID，1001：联赛 1002：孵化 1003 :  拼图
     * @Column(name="type", type="smallint", default=1001)
     */
    private $type;

    /**
     * @var string $name 活动名
     * @Column(name="name", type="string", length=255, default="")
     */
    private $name;

    /**
     * @var string $operator 发布通知的管理员
     * @Column(name="operator", type="string", length=50)
     * @Required()
     */
    private $operator;

    /**
     * @var string $extra 附加字段
     * @Column(name="extra", type="string", length=250)
     * @Required()
     */
    private $extra;

    /**
     * @var int $startAt 活动开始时间
     * @Column(name="start_at", type="integer", default=0)
     */
    private $startAt;

    /**
     * @var int $endAt 活动结束时间
     * @Column(name="end_at", type="integer", default=0)
     */
    private $endAt;

    /**
     * @var int $isDeleted 是否被删除：0，未删除；1，已删除。
     * @Column(name="is_deleted", type="tinyint", default=0)
     */
    private $isDeleted;

    /**
     * @var int $createdAt 创建时间
     * @Column(name="created_at", type="integer", default=0)
     */
    private $createdAt;

    /**
     * @var int $updatedAt 更新时间
     * @Column(name="updated_at", type="integer", default=0)
     */
    private $updatedAt;

    /**
     * 活动ID
     * @param int $value
     * @return $this
     */
    public function setId(int $value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * 活动类型ID，1001：联赛 1002：孵化 1003 :  拼图
     * @param int $value
     * @return $this
     */
    public function setType(int $value): self
    {
        $this->type = $value;

        return $this;
    }

    /**
     * 活动名
     * @param string $value
     * @return $this
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * 发布通知的管理员
     * @param string $value
     * @return $this
     */
    public function setOperator(string $value): self
    {
        $this->operator = $value;

        return $this;
    }

    /**
     * 附加字段
     * @param string $value
     * @return $this
     */
    public function setExtra(string $value): self
    {
        $this->extra = $value;

        return $this;
    }

    /**
     * 活动开始时间
     * @param int $value
     * @return $this
     */
    public function setStartAt(int $value): self
    {
        $this->startAt = $value;

        return $this;
    }

    /**
     * 活动结束时间
     * @param int $value
     * @return $this
     */
    public function setEndAt(int $value): self
    {
        $this->endAt = $value;

        return $this;
    }

    /**
     * 是否被删除：0，未删除；1，已删除。
     * @param int $value
     * @return $this
     */
    public function setIsDeleted(int $value): self
    {
        $this->isDeleted = $value;

        return $this;
    }

    /**
     * 创建时间
     * @param int $value
     * @return $this
     */
    public function setCreatedAt(int $value): self
    {
        $this->createdAt = $value;

        return $this;
    }

    /**
     * 更新时间
     * @param int $value
     * @return $this
     */
    public function setUpdatedAt(int $value): self
    {
        $this->updatedAt = $value;

        return $this;
    }

    /**
     * 活动ID
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 活动类型ID，1001：联赛 1002：孵化 1003 :  拼图
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 活动名
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 发布通知的管理员
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * 附加字段
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * 活动开始时间
     * @return int
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * 活动结束时间
     * @return int
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * 是否被删除：0，未删除；1，已删除。
     * @return int
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * 创建时间
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * 更新时间
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}
