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
 * @Table(name="center_reward_package")
 * @uses      CenterRewardPackage
 */
class CenterRewardPackage extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string $name 奖励礼包名称
     * @Column(name="name", type="string", length=255, default="")
     */
    private $name;

    /**
     * @var string $containList 礼包内容列表，json
     * @Column(name="contain_list", type="string", length=255, default="")
     */
    private $containList;

    /**
     * @var int $recieveNum 每个用户领取次数，默认是1
     * @Column(name="recieve_num", type="integer", default=1)
     */
    private $recieveNum;

    /**
     * @var int $isDeleted 是否已删除
     * @Column(name="is_deleted", type="tinyint")
     * @Required()
     */
    private $isDeleted;

    /**
     * @var int $isUsed 占用状态：0 未 使用 1 已使用
     * @Column(name="is_used", type="tinyint", default=0)
     */
    private $isUsed;

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
     * 奖励礼包名称
     * @param string $value
     * @return $this
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * 礼包内容列表，json
     * @param string $value
     * @return $this
     */
    public function setContainList(string $value): self
    {
        $this->containList = $value;

        return $this;
    }

    /**
     * 每个用户领取次数，默认是1
     * @param int $value
     * @return $this
     */
    public function setRecieveNum(int $value): self
    {
        $this->recieveNum = $value;

        return $this;
    }

    /**
     * 是否已删除
     * @param int $value
     * @return $this
     */
    public function setIsDeleted(int $value): self
    {
        $this->isDeleted = $value;

        return $this;
    }

    /**
     * 占用状态：0 未 使用 1 已使用
     * @param int $value
     * @return $this
     */
    public function setIsUsed(int $value): self
    {
        $this->isUsed = $value;

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
     * 奖励礼包名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 礼包内容列表，json
     * @return string
     */
    public function getContainList()
    {
        return $this->containList;
    }

    /**
     * 每个用户领取次数，默认是1
     * @return mixed
     */
    public function getRecieveNum()
    {
        return $this->recieveNum;
    }

    /**
     * 是否已删除
     * @return int
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * 占用状态：0 未 使用 1 已使用
     * @return int
     */
    public function getIsUsed()
    {
        return $this->isUsed;
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
