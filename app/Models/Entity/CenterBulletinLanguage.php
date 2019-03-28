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
 * @Table(name="center_bulletin_language")
 * @uses      CenterBulletinLanguage
 */
class CenterBulletinLanguage extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int $bulletinBoardId 公告板Id
     * @Column(name="bulletin_board_id", type="integer")
     * @Required()
     */
    private $bulletinBoardId;

    /**
     * @var int $isActived 是否是激活 0 未激活，1 已激活
     * @Column(name="is_actived", type="tinyint", default=1)
     */
    private $isActived;

    /**
     * @var string $language 语言类型
     * @Column(name="language", type="string", length=255)
     * @Required()
     */
    private $language;

    /**
     * @var string $banner 
     * @Column(name="banner", type="string", length=255)
     * @Required()
     */
    private $banner;

    /**
     * @var string $titleConfig 标题，一级页面标题配置 :[{"title":"wqe","position":[300,200],"size":21} ...]
     * @Column(name="title_config", type="text", length=65535)
     */
    private $titleConfig;

    /**
     * @var string $content 公告内容
     * @Column(name="content", type="text", length=4294967295)
     * @Required()
     */
    private $content;

    /**
     * @var int $createdAt 创建时间
     * @Column(name="created_at", type="integer")
     * @Required()
     */
    private $createdAt;

    /**
     * @var int $updatedAt 更新时间
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
     * 公告板Id
     * @param int $value
     * @return $this
     */
    public function setBulletinBoardId(int $value): self
    {
        $this->bulletinBoardId = $value;

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
     * @param string $value
     * @return $this
     */
    public function setBanner(string $value): self
    {
        $this->banner = $value;

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
     * 公告板Id
     * @return int
     */
    public function getBulletinBoardId()
    {
        return $this->bulletinBoardId;
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
     * 语言类型
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
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
