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
 * @Table(name="rule_criteria")
 * @uses      RuleCriteria
 */
class RuleCriteria extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int $ruleId 
     * @Column(name="ruleId", type="integer")
     * @Required()
     */
    private $ruleId;

    /**
     * @var int $aid 属性id
     * @Column(name="aid", type="integer")
     * @Required()
     */
    private $aid;

    /**
     * @var string $choices =,>,<,!=,>=,<=
     * @Column(name="choices", type="string", length=100)
     * @Required()
     */
    private $choices;

    /**
     * @var string $value 
     * @Column(name="value", type="string", length=100)
     * @Required()
     */
    private $value;

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
    public function setRuleId(int $value): self
    {
        $this->ruleId = $value;

        return $this;
    }

    /**
     * 属性id
     * @param int $value
     * @return $this
     */
    public function setAid(int $value): self
    {
        $this->aid = $value;

        return $this;
    }

    /**
     * =,>,<,!=,>=,<=
     * @param string $value
     * @return $this
     */
    public function setChoices(string $value): self
    {
        $this->choices = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

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
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * 属性id
     * @return int
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * =,>,<,!=,>=,<=
     * @return string
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
