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
 * @Table(name="score")
 * @uses      Score
 */
class Score extends Model
{
    /**
     * @var int $uid 用户ID，用来识别用户数据
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var int $level 关卡
     * @Id()
     * @Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @var int $score 得分
     * @Column(name="score", type="integer", default=0)
     */
    private $score;

    /**
     * @var int $star 星级
     * @Column(name="star", type="tinyint", default=0)
     */
    private $star;

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
     * 关卡
     * @param int $value
     * @return $this
     */
    public function setLevel(int $value)
    {
        $this->level = $value;

        return $this;
    }

    /**
     * 得分
     * @param int $value
     * @return $this
     */
    public function setScore(int $value): self
    {
        $this->score = $value;

        return $this;
    }

    /**
     * 星级
     * @param int $value
     * @return $this
     */
    public function setStar(int $value): self
    {
        $this->star = $value;

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
     * 关卡
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * 得分
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * 星级
     * @return int
     */
    public function getStar()
    {
        return $this->star;
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
