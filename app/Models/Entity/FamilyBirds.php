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
 * @Table(name="family_birds")
 * @uses      FamilyBirds
 */
class FamilyBirds extends Model
{
    /**
     * @var int $uid 
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var string $birdId 
     * @Id()
     * @Column(name="birdId", type="string", length=50)
     */
    private $birdId;

    /**
     * @var string $name 
     * @Column(name="name", type="string", length=50, default="")
     */
    private $name;

    /**
     * @var int $show 
     * @Column(name="show", type="tinyint", default=0)
     */
    private $show;

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
     * @param int $value
     * @return $this
     */
    public function setUid(int $value)
    {
        $this->uid = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setBirdId(string $value)
    {
        $this->birdId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setShow(int $value): self
    {
        $this->show = $value;

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
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return mixed
     */
    public function getBirdId()
    {
        return $this->birdId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getShow()
    {
        return $this->show;
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
