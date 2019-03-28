<?php

namespace App\Tlog;

use App\Base\BaseModel;

/**
 * tlog基类
 */
class TLog extends BaseModel
{
    protected $primary = '';
    
    public function scenarios()
    {
        return [
            'header' => [
                'gameSvrId',
                'clientTime',
                'vGameAppid',
                'platId',
                'iZoneAreaID',
                'vopenid'
            ],
            'tail' => [
                'deviceId',
                'clientIp',
                'uuid',
                'originAppVersion',
                'register_time',
                'registerAppVersion'
            ],
        ];
    }
    
    /**
     * 日志体
     * @var array
     */
    protected $_body = [];

   
    /**
     * (必填)登录的游戏服务器编号
     *
     * @var string
     */
    public $gameSvrId = 0;
    
    public $zoneId;
    
    public $areaId;

    /**
     * (必填)游戏事件的时间, 格式 YYYY-MM-DD HH:MM:SS
     *
     * @var datetime
     */
    public $clientTime = 'NULL';


    /**
     *(必填)游戏APPID
     *
     * @var string
     */
    public $vGameAppid = 'angry_bird_blast_qq';


    /**
     *(必填)ios 0 /android 1
     *
     * @var int
     */
    public $platId = 0;

    /**
     * 针对分区分服的游戏填写分区id，用来唯一标示一个区；非分区分服游戏请填写0
     *
     * @var int
     */
    public $iZoneAreaID = 0;

    /**
     * (必填)用户OPENID号
     *
     * @var string
     */
    public $vopenid = 'NULL';

    /**
     * (可选)设备ID
     *
     * @var string
     */
    public $deviceId = 'NULL';


    /**
     * (必填)客户端IP(后台服务器记录与玩家通信时的IP地址)
     * @var string
     */
    public $clientIp = 'NULL';

    /**
     * (必填)用户ID
     * var string
     */
    public $uuid = 'NULL';

    /**
     * (可选)客户端完整的版本号
     * var string
     */
    public $originAppVersion = 'NULL';

    /**
     * (必填)玩家注册的时间, 格式 YYYY-MM-DD HH:MM:SS
     * datetime
     */
    public $register_time = 'NULL';

    /**
     * (可选)玩家注册版本号
     * var string
     */
    public $registerAppVersion = "NULL";
    
    /**
     * 客户端版本
     * @var string
     */
    public $ClientVersion = 'NULL';


    function __construct(array $originLog = [])
    {
        //parent::__construct($originLog);
        $this->setHeader($originLog);
        
        $this->setBody($originLog);

        $this->setTail($originLog);

    }
    

    /**
     * 创建日志工具类
     * @access public
     * @param string $type 类型
     * @param array $originLog 原始数组
     *
     * @return object
     */
    public function factory($type = '', $originLog = array())
    {
        $class = 'App\\Tlog\\' . ucfirst($type);
        if (class_exists($class)) {
            $log = new $class($originLog);
        } else {
            return null;
        }

        return $log;
    }

    /**
     * 取得日志类实例
     *
     * @static
     * @access public
     * @return mixed
     */
    static function getInstance($type = '', $options = array())
    {

        $obj = new TLog($options);
        return $obj->factory($type, $options);//工厂方法创建不同类型的日志

    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function getBody()
    {
        return $this->getAttributes($this->scenarios()['body']);
    }

    public function getHeader()
    {
        return $this->getAttributes($this->scenarios()['header']);
    }

    public function getTail()
    {
        return $this->getAttributes($this->scenarios()['tail']);
    }

    public function setHeader($originLog)
    {
        $this->setAttributes($originLog);
        
    }
    
    public function setBody($originLog)
    {
        $this->setAttributes($originLog);
    }

    public function setTail($originLog)
    {
        $this->setAttributes($originLog);
    
        if(is_numeric($this->clientTime)){
            $this->clientTime = date('Y-m-d H:i:s', $this->clientTime);
        }
    
        if(is_numeric($this->register_time)){
            $this->register_time = date('Y-m-d H:i:s', $this->register_time);
        }
        
    }


    public function addOrReduce($log = [])
    {
        return ($log['num'] > 0) ? 0 : 1;
    }

    public function parse()
    {
        $str = '';
        if ($this->validate()) {
            $arr = $this->getHeader() + $this->getBody() + $this->getTail();
            $str = $this->primary . '|' . implode($arr, '|');
        }
        
        return $str;
    }


}