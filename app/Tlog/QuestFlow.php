<?php
namespace App\Tlog;

/**
 * Class QuestFlow
 * 剧情任务流水表(剧情任务完成触发日志)
 * @package app\tlog
 */
class QuestFlow extends TLog
{

    protected $primary = 'QuestFlow';

    /**
     * (必填)任务id
     * @var string
     */
    public $QuestID = 'NULL';
    /**
     * (必填)已完成任务数
     * @var int
     */
    public $Questlevel = 0;
    /**
     * (必填)任务类型 对应QuestType,其它说明参考FAQ文档
     * @var int
     */
    public $QuestType = 0;
    /**
     * (必填)剩余星星数
     * @var int
     */
    public $totalStar = 0;
    /**
     * (必填)剩余金币数
     * @var int
     */
    public $Gold = 0;
    /**
     * (必填)客户端版本
     * @var string
     */
    public $ClientVersion = 'NULL';
    /**
     * (必填)等级
     * @var int
     */
    public $Level = 0;
    /**
     * (必填)剩余银币数
     * @var int
     */
    public $silver = 0;
    /**
     * (必填)购买皮肤数
     * @var int
     */
    public $CountBuySkin = 0;
    
    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'QuestID',
                'Questlevel',
                'QuestType',
                'totalStar',
                'Gold',
                'ClientVersion'
            ],
            'tail' => array_merge(parent::scenarios()['tail'], [
                'Level',
                'silver',
                'CountBuySkin'
                
            ])
        ];
    }
    
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
    }

}