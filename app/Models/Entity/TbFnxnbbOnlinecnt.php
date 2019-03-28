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
 * @Table(name="tb_fnxnbb_onlinecnt")
 * @uses      TbFnxnbbOnlinecnt
 */
class TbFnxnbbOnlinecnt extends Model
{
    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string $gameappid 
     * @Column(name="gameappid", type="string", length=32, default="")
     */
    private $gameappid;

    /**
     * @var int $timekey 
     * @Column(name="timekey", type="integer", default=0)
     */
    private $timekey;

    /**
     * @var int $reporttime 上报服务器时间戳
     * @Column(name="reporttime", type="integer", default=0)
     */
    private $reporttime;

    /**
     * @var string $gsid 服务器编号
     * @Column(name="gsid", type="string", length=32, default="")
     */
    private $gsid;

    /**
     * @var int $zoneareaid 分区分服ID
     * @Column(name="zoneareaid", type="integer", default=0)
     */
    private $zoneareaid;

    /**
     * @var int $onlinecntios ios在线人数
     * @Column(name="onlinecntios", type="integer", default=0)
     */
    private $onlinecntios;

    /**
     * @var int $onlinecntandroid android在线人数
     * @Column(name="onlinecntandroid", type="integer", default=0)
     */
    private $onlinecntandroid;

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
    public function setGameappid(string $value): self
    {
        $this->gameappid = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setTimekey(int $value): self
    {
        $this->timekey = $value;

        return $this;
    }

    /**
     * 上报服务器时间戳
     * @param int $value
     * @return $this
     */
    public function setReporttime(int $value): self
    {
        $this->reporttime = $value;

        return $this;
    }

    /**
     * 服务器编号
     * @param string $value
     * @return $this
     */
    public function setGsid(string $value): self
    {
        $this->gsid = $value;

        return $this;
    }

    /**
     * 分区分服ID
     * @param int $value
     * @return $this
     */
    public function setZoneareaid(int $value): self
    {
        $this->zoneareaid = $value;

        return $this;
    }

    /**
     * ios在线人数
     * @param int $value
     * @return $this
     */
    public function setOnlinecntios(int $value): self
    {
        $this->onlinecntios = $value;

        return $this;
    }

    /**
     * android在线人数
     * @param int $value
     * @return $this
     */
    public function setOnlinecntandroid(int $value): self
    {
        $this->onlinecntandroid = $value;

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
    public function getGameappid()
    {
        return $this->gameappid;
    }

    /**
     * @return int
     */
    public function getTimekey()
    {
        return $this->timekey;
    }

    /**
     * 上报服务器时间戳
     * @return int
     */
    public function getReporttime()
    {
        return $this->reporttime;
    }

    /**
     * 服务器编号
     * @return string
     */
    public function getGsid()
    {
        return $this->gsid;
    }

    /**
     * 分区分服ID
     * @return int
     */
    public function getZoneareaid()
    {
        return $this->zoneareaid;
    }

    /**
     * ios在线人数
     * @return int
     */
    public function getOnlinecntios()
    {
        return $this->onlinecntios;
    }

    /**
     * android在线人数
     * @return int
     */
    public function getOnlinecntandroid()
    {
        return $this->onlinecntandroid;
    }

}
