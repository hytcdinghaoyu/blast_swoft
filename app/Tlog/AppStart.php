<?php
namespace App\Tlog;

//新客户端打开流水表（APP第一次打开时触发日志）
class AppStart extends TLog
{

    protected $primary = 'AppStart';

    /**
     * (必填)新客户端类型 对应StartType,其它说明参考FAQ文档
     * @var int
     */
    public $StartType = 0;
    /**
     * (必填)客户端版本
     * @var string
     */
    public $ClientVersion = 'NULL';
    
    public function scenarios()
    {
        return [
            'header' => [
                'gameSvrId',
                'clientTime',
                'vGameAppid',
                'platId',
            ],
            'body' => [
                'StartType',
                'ClientVersion'
            ],
            'tail' => [
                'deviceId',
                'clientIp',
                'originAppVersion'
            ]
        ];
    }

    function __construct($originLog = [])
    {
        parent::__construct($originLog);
    }
    


}