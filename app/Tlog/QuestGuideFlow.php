<?php
namespace App\Tlog;

//剧情新手引导信息表(每个引导节点结束触发日志)
class QuestGuideFlow extends TLog
{

    protected $primary = 'QuestGuideFlow';

    /**
     * (必填)节点ID(引导所在剧情ID)
     * @var string
     */
    public $iGuideID = 'NULL';
    /**
     * (可选)等级
     * @var int
     */
    public $iLevel = 0;
    /**
     * (必填)玩家角色ID
     * @var string
     */
    public $vRoleID = 'NULL';
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
                'iGuideID',
                'iLevel',
                'vRoleID',
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