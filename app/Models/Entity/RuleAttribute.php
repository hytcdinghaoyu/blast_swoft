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
 * 规则属性

 * @Entity(instance="global")
 * @Table(name="rule_attribute")
 * @uses      RuleAttribute
 */
class RuleAttribute extends Model
{
    /**
     * @var int $aid 属性id
     * @Id()
     * @Column(name="aid", type="integer")
     */
    private $aid;

    /**
     * @var string $attrName 属性名
     * @Column(name="attr_name", type="string", length=100)
     * @Required()
     */
    private $attrName;

    /**
     * @var string $choices =,>,<,!=,>=,<=
     * @Column(name="choices", type="string", length=100)
     * @Required()
     */
    private $choices;

    /**
     * @var int $attrType 1-数字 2-日期 3-string 4-单选 5-多选
     * @Column(name="attrType", type="integer")
     * @Required()
     */
    private $attrType;

    /**
     * @var int $groupId 
     * @Column(name="groupId", type="integer")
     * @Required()
     */
    private $groupId;

    /**
     * @var string $groupName 
     * @Column(name="groupName", type="string", length=100)
     * @Required()
     */
    private $groupName;

    /**
     * @var string $value 默认值
     * @Column(name="value", type="text", length=65535)
     */
    private $value;

    /**
     * @var string $regular 校验规则
     * @Column(name="regular", type="string", length=100)
     */
    private $regular;

    /**
     * 属性id
     * @param int $value
     * @return $this
     */
    public function setAid(int $value)
    {
        $this->aid = $value;

        return $this;
    }

    /**
     * 属性名
     * @param string $value
     * @return $this
     */
    public function setAttrName(string $value): self
    {
        $this->attrName = $value;

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
     * 1-数字 2-日期 3-string 4-单选 5-多选
     * @param int $value
     * @return $this
     */
    public function setAttrType(int $value): self
    {
        $this->attrType = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setGroupId(int $value): self
    {
        $this->groupId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setGroupName(string $value): self
    {
        $this->groupName = $value;

        return $this;
    }

    /**
     * 默认值
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * 校验规则
     * @param string $value
     * @return $this
     */
    public function setRegular(string $value): self
    {
        $this->regular = $value;

        return $this;
    }

    /**
     * 属性id
     * @return mixed
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * 属性名
     * @return string
     */
    public function getAttrName()
    {
        return $this->attrName;
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
     * 1-数字 2-日期 3-string 4-单选 5-多选
     * @return int
     */
    public function getAttrType()
    {
        return $this->attrType;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * 默认值
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 校验规则
     * @return string
     */
    public function getRegular()
    {
        return $this->regular;
    }

}
