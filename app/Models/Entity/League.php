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
 * @Table(name="league")
 * @uses      League
 */
class League extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="bigint")
     */
    private $id;

    /**
     * @var int $uid 用户uid
     * @Column(name="uid", type="bigint")
     * @Required()
     */
    private $uid;

    /**
     * @var int $upDown 相对于上赛季上升或下降
     * @Column(name="upDown", type="tinyint", default=0)
     */
    private $upDown;

    /**
     * @var int $rank 段位
     * @Column(name="rank", type="tinyint", default=0)
     */
    private $rank;

    /**
     * @var int $createdAt 创建时间：时间戳
     * @Column(name="created_at", type="integer", default=0)
     */
    private $createdAt;

    /**
     * @var int $updatedAt 更新时间：时间戳
     * @Column(name="updated_at", type="integer", default=0)
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
     * 用户uid
     * @param int $value
     * @return $this
     */
    public function setUid(int $value): self
    {
        $this->uid = $value;

        return $this;
    }

    /**
     * 相对于上赛季上升或下降
     * @param int $value
     * @return $this
     */
    public function setUpDown(int $value): self
    {
        $this->upDown = $value;

        return $this;
    }

    /**
     * 段位
     * @param int $value
     * @return $this
     */
    public function setRank(int $value): self
    {
        $this->rank = $value;

        return $this;
    }

    /**
     * 创建时间：时间戳
     * @param int $value
     * @return $this
     */
    public function setCreatedAt(int $value): self
    {
        $this->createdAt = $value;

        return $this;
    }

    /**
     * 更新时间：时间戳
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
     * 用户uid
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * 相对于上赛季上升或下降
     * @return int
     */
    public function getUpDown()
    {
        return $this->upDown;
    }

    /**
     * 段位
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * 创建时间：时间戳
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * 更新时间：时间戳
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}
