<?php
namespace App\Tlog;

class AccountFlow extends TLog
{

    protected $primary = 'MoneyFlow';

    /**
     * 增加或者减少
     */
    CONST ADD = 0;
    CONST REDUCE = 1;

    /**
     * 货币类型
     */
    CONST MT_GOLD = 0;//金币
    CONST MT_SILVER = 1;//银币

    /**
     * (可选)用于关联一次动作产生多条不同类型的货币流动日志
     * @var string
     */
    public $Sequence = 0;

    /**
     * (必填)玩家等级
     * @var int
     */
    public $topLevel = 0;

    /**
     * (可选)动作后的金钱数
     * @var int
     */
    public $AfterMoney = 0;

    /**
     * (必填)动作涉及的金钱数
     * @var int
     */
    public $iMoney = 0;

    /**
     * (必填)货币流动一级原因
     * @var string
     */
    public $Reason = 'NULL';

    /**
     * (必填)货币流动二级原因
     * @var string
     */
    public $SubReason = 0;

    /**
     * (必填)增加 0/减少 1
     * @var int
     */
    public $AddOrReduce = 0;

    /**
     * 钱币类型
     * @var int
     */
    public $iMoneyType = 0;

    /**
     * 客户端版本
     * @var string
     */
    public $ClientVersion = 'NULL';


    /**
     * 动作后的金钱数
     */
    private function getAfterMoney($log = [])
    {
        $afterMoney = 0;
        if ($log && in_array($log['actionType'], [1, 2])) {
            $log['actionType'] = $log['actionType'] % 2;
            if ($log['actionType'] == self::MT_GOLD) {
                $afterMoney = $log['gold'] + $log['num'];
            } else {
                $afterMoney = $log['silver'] + $log['num'];
            }
        }
        return $afterMoney;
    }
    
    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'Sequence',
                'topLevel',
                'AfterMoney',
                'iMoney',
                'Reason',
                'SubReason',
                'AddOrReduce',
                'iMoneyType',
                'ClientVersion',
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
        $this->AfterMoney = $this->getAfterMoney($originLog);
        $this->iMoney = abs($originLog['num']) ?? $this->iMoney;
        $this->Reason = $originLog['flowAction'] ?? $this->Reason;
        $this->SubReason = $originLog['flowAction'] ?? $this->SubReason;
        $this->AddOrReduce = $this->addOrReduce($originLog) ?? $this->AddOrReduce;
        $this->iMoneyType = isset($originLog['actionType']) ? $originLog['actionType'] % 2 : $this->iMoneyType;
        
    }


}