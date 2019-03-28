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
 * @Table(name="order")
 * @uses      Order
 */
class Order extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="bigint")
     */
    private $id;

    /**
     * @var int $uid 
     * @Column(name="uid", type="bigint", default=0)
     */
    private $uid;

    /**
     * @var string $billno 订单号，全局唯一
     * @Column(name="billno", type="string", length=100, default="")
     */
    private $billno;

    /**
     * @var int $money rmb金额变动数
     * @Column(name="money", type="integer", default=0)
     */
    private $money;

    /**
     * @var int $coin 代币金额变动数
     * @Column(name="coin", type="integer", default=0)
     */
    private $coin;

    /**
     * @var string $productId 礼包id
     * @Column(name="product_id", type="string", length=50, default="")
     */
    private $productId;

    /**
     * @var string $payItem 购买的道具礼包详情
     * @Column(name="pay_item", type="string", length=255, default="")
     */
    private $payItem;

    /**
     * @var int $status 
     * @Column(name="status", type="tinyint", default=0)
     */
    private $status;

    /**
     * @var int $type 
     * @Column(name="type", type="tinyint", default=0)
     */
    private $type;

    /**
     * @var int $updatedAt 
     * @Column(name="updated_at", type="integer", default=0)
     */
    private $updatedAt;

    /**
     * @var int $createdAt 
     * @Column(name="created_at", type="integer", default=0)
     */
    private $createdAt;

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
     * @param int $value
     * @return $this
     */
    public function setUid(int $value): self
    {
        $this->uid = $value;

        return $this;
    }

    /**
     * 订单号，全局唯一
     * @param string $value
     * @return $this
     */
    public function setBillno(string $value): self
    {
        $this->billno = $value;

        return $this;
    }

    /**
     * rmb金额变动数
     * @param int $value
     * @return $this
     */
    public function setMoney(int $value): self
    {
        $this->money = $value;

        return $this;
    }

    /**
     * 代币金额变动数
     * @param int $value
     * @return $this
     */
    public function setCoin(int $value): self
    {
        $this->coin = $value;

        return $this;
    }

    /**
     * 礼包id
     * @param string $value
     * @return $this
     */
    public function setProductId(string $value): self
    {
        $this->productId = $value;

        return $this;
    }

    /**
     * 购买的道具礼包详情
     * @param string $value
     * @return $this
     */
    public function setPayItem(string $value): self
    {
        $this->payItem = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setStatus(int $value): self
    {
        $this->status = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setType(int $value): self
    {
        $this->type = $value;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * 订单号，全局唯一
     * @return string
     */
    public function getBillno()
    {
        return $this->billno;
    }

    /**
     * rmb金额变动数
     * @return int
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * 代币金额变动数
     * @return int
     */
    public function getCoin()
    {
        return $this->coin;
    }

    /**
     * 礼包id
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * 购买的道具礼包详情
     * @return string
     */
    public function getPayItem()
    {
        return $this->payItem;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
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
