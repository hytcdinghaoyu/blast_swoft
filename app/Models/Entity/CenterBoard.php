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
 * @Table(name="center_board")
 * @uses      CenterBoard
 */
class CenterBoard extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string $name 
     * @Column(name="name", type="string", length=255)
     * @Required()
     */
    private $name;

    /**
     * @var int $isActived 是否是激活 0 未激活，1 已激活
     * @Column(name="is_actived", type="tinyint", default=1)
     */
    private $isActived;

    /**
     * @var int $isForce 
     * @Column(name="is_force", type="tinyint", default=0)
     */
    private $isForce;

    /**
     * @var string $language 语言类型
     * @Column(name="language", type="string", length=255)
     * @Required()
     */
    private $language;

    /**
     * @var string $titleConfig 标题，一级页面标题配置 :[{"title":"wqe","position":[300,200],"size":21} ...]
     * @Column(name="title_config", type="text", length=65535)
     * @Required()
     */
    private $titleConfig;

    /**
     * @var string $content 公告内容
     * @Column(name="content", type="text", length=4294967295)
     * @Required()
     */
    private $content;

    /**
     * @var string $extra 
     * @Column(name="extra", type="string", length=255, default="")
     */
    private $extra;

    /**
     * @var int $startAt 开始时间
     * @Column(name="start_at", type="integer")
     * @Required()
     */
    private $startAt;

    /**
     * @var int $endAt 结束时间
     * @Column(name="end_at", type="integer")
     * @Required()
     */
    private $endAt;

    /**
     * @var int $createdAt 创建时间
     * @Column(name="created_at", type="integer")
     */
    private $createdAt;

    /**
     * @var int $updatedAt 更新时间
     * @Column(name="updated_at", type="integer")
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
     * @param string $value
     * @return $this
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * 是否是激活 0 未激活，1 已激活
     * @param int $value
     * @return $this
     */
    public function setIsActived(int $value): self
    {
        $this->isActived = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setIsForce(int $value): self
    {
        $this->isForce = $value;

        return $this;
    }

    /**
     * 语言类型
     * @param string $value
     * @return $this
     */
    public function setLanguage(string $value): self
    {
        $this->language = $value;

        return $this;
    }

    /**
     * 标题，一级页面标题配置 :[{"title":"wqe","position":[300,200],"size":21} ...]
     * @param string $value
     * @return $this
     */
    public function setTitleConfig(string $value): self
    {
        $this->titleConfig = $value;

        return $this;
    }

    /**
     * 公告内容
     * @param string $value
     * @return $this
     */
    public function setContent(string $value): self
    {
        $this->content = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setExtra(string $value): self
    {
        $this->extra = $value;

        return $this;
    }

    /**
     * 开始时间
     * @param int $value
     * @return $this
     */
    public function setStartAt(int $value): self
    {
        $this->startAt = $value;

        return $this;
    }

    /**
     * 结束时间
     * @param int $value
     * @return $this
     */
    public function setEndAt(int $value): self
    {
        $this->endAt = $value;

        return $this;
    }

    /**
     * 创建时间
     * @param int $value
     * @return $this
     */
    public function setCreatedAt(int $value): self
    {
        $this->createdAt = $value;

        return $this;
    }

    /**
     * 更新时间
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 是否是激活 0 未激活，1 已激活
     * @return mixed
     */
    public function getIsActived()
    {
        return $this->isActived;
    }

    /**
     * @return int
     */
    public function getIsForce()
    {
        return $this->isForce;
    }

    /**
     * 语言类型
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * 标题，一级页面标题配置 :[{"title":"wqe","position":[300,200],"size":21} ...]
     * @return string
     */
    public function getTitleConfig()
    {
        return $this->titleConfig;
    }

    /**
     * 公告内容
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * 开始时间
     * @return int
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * 结束时间
     * @return int
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * 创建时间
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * 更新时间
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}
