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
 * 规则配置

 * @Entity(instance="global")
 * @Table(name="rule_configurations")
 * @uses      RuleConfigurations
 */
class RuleConfigurations extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int $ruleId ruleId
     * @Column(name="ruleId", type="integer")
     * @Required()
     */
    private $ruleId;

    /**
     * @var int $type 类型 1-json 2-filePath
     * @Column(name="type", type="tinyint")
     * @Required()
     */
    private $type;

    /**
     * @var string $value 值
     * @Column(name="value", type="text", length=65535)
     * @Required()
     */
    private $value;

    /**
     * @var string $md5 
     * @Column(name="md5", type="string", length=32)
     */
    private $md5;

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
     * ruleId
     * @param int $value
     * @return $this
     */
    public function setRuleId(int $value): self
    {
        $this->ruleId = $value;

        return $this;
    }

    /**
     * 类型 1-json 2-filePath
     * @param int $value
     * @return $this
     */
    public function setType(int $value): self
    {
        $this->type = $value;

        return $this;
    }

    /**
     * 值
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMd5(string $value): self
    {
        $this->md5 = $value;

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
     * ruleId
     * @return int
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * 类型 1-json 2-filePath
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 值
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMd5()
    {
        return $this->md5;
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
