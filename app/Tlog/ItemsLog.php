<?php
namespace App\Tlog;

class ItemsLog extends TLog
{

    protected $primary = 'ItemFlow';

    CONST ADD = 0;
    CONST REDUCE = 1;

    //iGoodsType
    CONST IN_LEVEL = 0;
    CONST BEFORE_LEVEL = 1;

    //iUseType
    CONST DEFAULT_LEVEL = 0;//主线关卡
    CONST ACTIVE_LEVEL = 1;//策划活动
    
    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'level',
                'Sequence',
                'iGoodsType',
                'iGoodsId',
                'Count',
                'AfterCount',
                'Reason',
                'SubReason',
                'iMoney',
                'iMoneyType',
                'AddOrReduce',
                'iUseType',
                'ClientVersion',
            ],
            'tail' => array_merge(parent::scenarios()['tail'], [
                'BattleID'
            ])
        ];
    }

    public $level = 0;

    public $Sequence = 0;

    public $iGoodsType = 0;

    public $iGoodsId = 0;

    public $Count = 0;

    public $AfterCount = 0;

    public $Reason = 0;

    public $SubReason = 0;

    public $iMoney = 0;

    public $iMoneyType = 0;

    public $AddOrReduce = 1;

    public $iUseType = 0;

    public $ClientVersion = 'NULL';

    public $BattleID = 0;


    /**
     * 日志体
     */
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
        
        $this->AddOrReduce = $this->addOrReduce($originLog) ?? $this->AddOrReduce;
        $this->iMoneyType = $originLog['actionType'] ?? $this->iMoneyType;
        $this->SubReason = $originLog['flowAction'] ?? $this->SubReason;
        $this->Reason = $originLog['flowAction'] ?? $this->Reason;
        $this->Count = abs($originLog['num']) ?? $this->Count;
        $this->iGoodsId = $originLog['item_id'] ?? $this->iGoodsId;
    }
    

}