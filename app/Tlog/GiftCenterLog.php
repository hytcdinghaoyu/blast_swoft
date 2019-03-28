<?php
namespace App\Tlog;

class GiftCenterLog extends TLog
{

    protected $primary = 'GiftCenterLog';


    public $AreaId = 0;

    public $PlatId = 0;

    public $OpenId = '';

    public $RewardId = 0;

    public $RewardContent = '';

    public $Source = 0;

    public $Serial = '';


    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'AreaId',
                'PlatId',
                'OpenId',
                'RewardId',
                'RewardContent',
                'Source',
                'Serial'
            ],
            'tail' => parent::scenarios()['tail']
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