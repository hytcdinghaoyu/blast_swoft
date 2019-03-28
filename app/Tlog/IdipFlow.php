<?php
namespace App\Tlog;

class IdipFlow extends TLog
{

    protected $primary = 'IdipFlow';

    public $GameSvrId = '';

    public $dtEventTime = '';

    public $GameAppID = 'fnxn';

    public $OpenId = '';

    public $PlatId = 0;

    public $AreaId = 0;

    public $ZoneId = 0;

    public $ItemId = 0;

    public $ItemNum = 0;

    public $Source = 0;

    public $Serial = '';

    public $CmdId = '';



    public function scenarios()
    {
        return [
            'header' => [],
            'body' => [
                'GameSvrId',
                'dtEventTime',
                'GameAppID',
                'OpenId',
                'PlatId',
                'AreaId',
                'ZoneId',
                'ItemId',
                'ItemNum',
                'Source',
                'Serial',
                'CmdId',
            ],
            'tail' =>[]
        ];
    }




}