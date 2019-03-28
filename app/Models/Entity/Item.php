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
 * @Table(name="item")
 * @uses      Item
 */
class Item extends Model
{
    /**
     * @var int $uid 用户ID，用来识别用户数据
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var int $item 道具ID
     * @Id()
     * @Column(name="item", type="smallint")
     */
    private $item;

    /**
     * @var int $number 道具数量
     * @Column(name="number", type="integer", default=0)
     */
    private $number;

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
     * 道具ID
     * @param int $value
     * @return $this
     */
    public function setItem(int $value)
    {
        $this->item = $value;

        return $this;
    }

    /**
     * 道具数量
     * @param int $value
     * @return $this
     */
    public function setNumber(int $value): self
    {
        $this->number = $value;

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
     * 道具ID
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * 道具数量
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
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
