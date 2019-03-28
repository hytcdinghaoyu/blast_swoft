<?php
namespace App\Tlog;

class GameSvrState extends TLog
{

    protected $primary = 'GameSvrState';

    public $dtEventTime = 'NULL';

    public $vGameIP = 'NULL';

    public $iZoneAreaID = 0;
    
    
    public function scenarios()
    {
        return [
            'header' => [],
            'body' => [
                'dtEventTime',
                'vGameIP',
                'iZoneAreaID'
            ],
            'tail' => []
        ];
    }
    

    /**
     * 日志体
     */
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
    }
    

}