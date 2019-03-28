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
 * 规则

 * @Entity(instance="global")
 * @Table(name="rule")
 * @uses      Rule
 */
class Rule extends Model
{
    /**
     * @var int $ruleId 
     * @Id()
     * @Column(name="ruleId", type="integer")
     */
    private $ruleId;

    /**
     * @var string $ruleName 
     * @Column(name="ruleName", type="string", length=256)
     * @Required()
     */
    private $ruleName;

    /**
     * @var int $status 1-on 2-off 3-archived
     * @Column(name="status", type="tinyint")
     * @Required()
     */
    private $status;

    /**
     * @var int $type 1-basic 2-Experiment
     * @Column(name="type", type="tinyint")
     * @Required()
     */
    private $type;

    /**
     * @var int $scheduleStart 开始时间（为0不限制）
     * @Column(name="scheduleStart", type="integer")
     * @Required()
     */
    private $scheduleStart;

    /**
     * @var int $scheduleEnd 结束时间（为0不限制）
     * @Column(name="scheduleEnd", type="integer")
     * @Required()
     */
    private $scheduleEnd;

    /**
     * @var string $description 
     * @Column(name="description", type="string", length=1024)
     * @Required()
     */
    private $description;

    /**
     * @var int $parentRule 父规则  0-顶级规则
     * @Column(name="parentRule", type="integer")
     * @Required()
     */
    private $parentRule;

    /**
     * @var int $isParent 该类目是否为父类目(即：该类目是否还有子类目) 1-是 0-不是
     * @Column(name="isParent", type="tinyint")
     * @Required()
     */
    private $isParent;

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
    public function setRuleId(int $value)
    {
        $this->ruleId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setRuleName(string $value): self
    {
        $this->ruleName = $value;

        return $this;
    }

    /**
     * 1-on 2-off 3-archived
     * @param int $value
     * @return $this
     */
    public function setStatus(int $value): self
    {
        $this->status = $value;

        return $this;
    }

    /**
     * 1-basic 2-Experiment
     * @param int $value
     * @return $this
     */
    public function setType(int $value): self
    {
        $this->type = $value;

        return $this;
    }

    /**
     * 开始时间（为0不限制）
     * @param int $value
     * @return $this
     */
    public function setScheduleStart(int $value): self
    {
        $this->scheduleStart = $value;

        return $this;
    }

    /**
     * 结束时间（为0不限制）
     * @param int $value
     * @return $this
     */
    public function setScheduleEnd(int $value): self
    {
        $this->scheduleEnd = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDescription(string $value): self
    {
        $this->description = $value;

        return $this;
    }

    /**
     * 父规则  0-顶级规则
     * @param int $value
     * @return $this
     */
    public function setParentRule(int $value): self
    {
        $this->parentRule = $value;

        return $this;
    }

    /**
     * 该类目是否为父类目(即：该类目是否还有子类目) 1-是 0-不是
     * @param int $value
     * @return $this
     */
    public function setIsParent(int $value): self
    {
        $this->isParent = $value;

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
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * @return string
     */
    public function getRuleName()
    {
        return $this->ruleName;
    }

    /**
     * 1-on 2-off 3-archived
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 1-basic 2-Experiment
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 开始时间（为0不限制）
     * @return int
     */
    public function getScheduleStart()
    {
        return $this->scheduleStart;
    }

    /**
     * 结束时间（为0不限制）
     * @return int
     */
    public function getScheduleEnd()
    {
        return $this->scheduleEnd;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 父规则  0-顶级规则
     * @return int
     */
    public function getParentRule()
    {
        return $this->parentRule;
    }

    /**
     * 该类目是否为父类目(即：该类目是否还有子类目) 1-是 0-不是
     * @return int
     */
    public function getIsParent()
    {
        return $this->isParent;
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
