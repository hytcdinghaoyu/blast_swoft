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
 * @Table(name="task")
 * @uses      Task
 */
class Task extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="bigint")
     */
    private $id;

    /**
     * @var int $uid 用户ID，用来识别用户数据
     * @Column(name="uid", type="bigint")
     * @Required()
     */
    private $uid;

    /**
     * @var int $taskId 任务ID
     * @Column(name="task_id", type="integer")
     * @Required()
     */
    private $taskId;

    /**
     * @var int $rewardTime 任务完成时获取任务奖励的时间，未获取完成的任务为0
     * @Column(name="reward_time", type="integer", default=0)
     */
    private $rewardTime;

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
     * @param int $value
     * @return $this
     */
    public function setId(int $value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * 用户ID，用来识别用户数据
     * @param int $value
     * @return $this
     */
    public function setUid(int $value): self
    {
        $this->uid = $value;

        return $this;
    }

    /**
     * 任务ID
     * @param int $value
     * @return $this
     */
    public function setTaskId(int $value): self
    {
        $this->taskId = $value;

        return $this;
    }

    /**
     * 任务完成时获取任务奖励的时间，未获取完成的任务为0
     * @param int $value
     * @return $this
     */
    public function setRewardTime(int $value): self
    {
        $this->rewardTime = $value;

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
     * 用户ID，用来识别用户数据
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * 任务ID
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * 任务完成时获取任务奖励的时间，未获取完成的任务为0
     * @return int
     */
    public function getRewardTime()
    {
        return $this->rewardTime;
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
