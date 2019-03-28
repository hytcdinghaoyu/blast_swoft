<?php
namespace App\Tlog;

class LogAnti extends TLog
{

    protected $primary = 'LogAntiData';

    public $iSequence = 0;

    public $strData = 'NULL';
    
    public function scenarios()
    {
        return [
            'header' => [],
            'body' => [
                'gameSvrId',
                'clientTime',
                'iSequence',
                'vGameAppid',
                'vopenid',
                'platId',
                'strData'
            ],
            'tail' => []
        ];
    }
    
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
    }
    
}