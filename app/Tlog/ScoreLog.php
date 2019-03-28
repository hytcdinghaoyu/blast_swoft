<?php

namespace app\tlog;

/**
 * 单局结束数据流水
 * Class Scorelog
 */
class ScoreLog extends TLog
{

    protected $primary = 'RoundFlow';

    CONST SUCCESS = 1;//成功
    CONST FAIL = 0;//失败

    CONST BATTLE_PVE = 0;//单人游戏
    CONST BATTLE_PVP = 1;//对战游戏
    CONST BATTLE_OTHER = 2;//其他对局
    
    public function scenarios()
    {
        return [
            'header' => [
                'gameSvrId',
                'clientTime',
                'vGameAppid',
                'platId',
                'iZoneAreaID',
                'vopenid'
            ],
            'body' => [
                'BattleID',
                'BattleType',
                'RoundScore',
                'RoundTime' ,
                'Result',
                'Rank' ,
                'gold' ,
                'ClientVersion'
            ],
            'tail' => array_merge(parent::scenarios()['tail'], [
                'totalStar',
                'BattleItem',
                'SurplusStep',
                'LevelGoals',
                'BattleGoals',
                'map_name',
                'silver',
                'difficult',
                'lives',
                'topLevel'
            ]),
        ];
    }

    /**
     * (必填)关卡id或者副本id[副本ID必须有序]，对局时填0
     * @var int
     */
    public $BattleID = 0;

    /**
     * (必填)战斗类型 对应BATTLETYPE,其它说明参考FAQ文档
     * @var int
     */
    public $BattleType = 0;

    /**
     * (必填)本局分数,无得分的填0
     * @var int
     */
    public $RoundScore = 0;

    /**
     * (必填)对局时长(秒)
     * @var int
     */
    public $RoundTime = 0;

    /**
     * (必填)单局结果，参考 Result
     * @var int
     */
    public $Result = 0;

    /**
     * 排名
     * @var int
     */
    public $Rank = 0;

    /**
     * 金钱
     * @var int
     */
    public $gold = 0;
    

    public $totalStar = 0;

    public $BattleItem = '';

    public $SurplusStep = 0;

    public $LevelGoals = 0;

    public $BattleGoals = 0;

    /**
     * 关卡包类型
     * @var string
     */
    public $map_name = 'NULL';

    /**
     * 关卡难度，难关为1，普通关卡为0
     * @var int
     */
    public $difficult = 0;

    /**
     * （必填)剩余银币数
     * @var int
     */
    public $silver = 0;

    /**
     * (必填)剩余体力数
     * @var int
     */
    public $lives = 0;
    
    //最高关卡数
    public $topLevel = 0;


    /**
     * 日志体
     */
    function __construct($originLog = [])
    {
        parent::__construct($originLog);
        $this->BattleID = $originLog['level'] ?? $this->BattleID;
        $this->RoundScore = $originLog['score'] ?? $this->RoundScore;
        $this->RoundTime = $originLog['duration'] ?? $this->RoundTime;
        $this->Result = $originLog['win'] ?? $this->Result;
    }


}