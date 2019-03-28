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
 * @Table(name="family_quest")
 * @uses      FamilyQuest
 */
class FamilyQuest extends Model
{
    /**
     * @var int $uid 
     * @Id()
     * @Column(name="uid", type="bigint")
     */
    private $uid;

    /**
     * @var string $questId 
     * @Id()
     * @Column(name="questId", type="string", length=50)
     */
    private $questId;

    /**
     * @var string $step 
     * @Column(name="step", type="string", length=100, default="")
     */
    private $step;

    /**
     * @var string $ownSteps 
     * @Column(name="own_steps", type="string", length=100, default="")
     */
    private $ownSteps;

    /**
     * @var int $show 
     * @Column(name="show", type="tinyint", default=0)
     */
    private $show;

    /**
     * @var int $state 0:初始状态,1:完成状态,2:时间任务开始状态,3:时间任务结束等待确认
     * @Column(name="state", type="tinyint", default=0)
     */
    private $state;

    /**
     * @var int $draw 0:未触发,1:已触发,默认值为:0
     * @Column(name="draw", type="tinyint", default=0)
     */
    private $draw;

    /**
     * @var int $initPlay 
     * @Column(name="init_play", type="tinyint", default=0)
     */
    private $initPlay;

    /**
     * @var int $sTime 
     * @Column(name="s_time", type="integer", default=0)
     */
    private $sTime;

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
    public function setQuestId(string $value)
    {
        $this->questId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setStep(string $value): self
    {
        $this->step = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setOwnSteps(string $value): self
    {
        $this->ownSteps = $value;

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
     * 0:初始状态,1:完成状态,2:时间任务开始状态,3:时间任务结束等待确认
     * @param int $value
     * @return $this
     */
    public function setState(int $value): self
    {
        $this->state = $value;

        return $this;
    }

    /**
     * 0:未触发,1:已触发,默认值为:0
     * @param int $value
     * @return $this
     */
    public function setDraw(int $value): self
    {
        $this->draw = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setInitPlay(int $value): self
    {
        $this->initPlay = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setSTime(int $value): self
    {
        $this->sTime = $value;

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
    public function getQuestId()
    {
        return $this->questId;
    }

    /**
     * @return string
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @return string
     */
    public function getOwnSteps()
    {
        return $this->ownSteps;
    }

    /**
     * @return int
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * 0:初始状态,1:完成状态,2:时间任务开始状态,3:时间任务结束等待确认
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * 0:未触发,1:已触发,默认值为:0
     * @return int
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * @return int
     */
    public function getInitPlay()
    {
        return $this->initPlay;
    }

    /**
     * @return int
     */
    public function getSTime()
    {
        return $this->sTime;
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
