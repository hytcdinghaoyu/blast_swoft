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
 * @Table(name="my_friends")
 * @uses      MyFriends
 */
class MyFriends extends Model
{
    /**
     * @var int $uid 用户ID，用来识别用户数据
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var int $fuid 好友的uid
     * @Id()
     * @Column(name="fuid", type="bigint")
     */
    private $fuid;

    /**
     * @var int $updatedAt 
     * @Column(name="updated_at", type="integer")
     */
    private $updatedAt;

    /**
     * @var int $createdAt 
     * @Column(name="created_at", type="integer")
     */
    private $createdAt;

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
     * 好友的uid
     * @param int $value
     * @return $this
     */
    public function setFuid(int $value)
    {
        $this->fuid = $value;

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
     * @param int $value
     * @return $this
     */
    public function setCreatedAt(int $value): self
    {
        $this->createdAt = $value;

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
     * 好友的uid
     * @return mixed
     */
    public function getFuid()
    {
        return $this->fuid;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}
