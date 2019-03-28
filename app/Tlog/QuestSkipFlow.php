<?php

namespace App\Tlog;

/**
 * Class QuestFlow
 * 剧情对话跳过流水表(剧情对话点击skip触发日志)
 * @package app\tlog
 */
class QuestSkipFlow extends TLog
{

    protected $primary = 'QuestSkipFlow';

    /**
     * (必填)任务id
     * @var string
     */
    public $QuestID = 'NULL';
    /**
     * 玩家点击skip时，弹板显示对话的ID，玩家没有点击过skip时为NULL
     * @var string
     */
    public $DialogueSkip = 'NULL';
    /**
     * (必填)客户端版本
     * @var string
     */
    public $ClientVersion = 'NULL';
    
    public function scenarios()
    {
        return [
            'header' => parent::scenarios()['header'],
            'body' => [
                'QuestID',
                'DialogueSkip',
                'ClientVersion'
            ],
            'tail' => parent::scenarios()['tail'],
        ];
    }
    
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
    }


}