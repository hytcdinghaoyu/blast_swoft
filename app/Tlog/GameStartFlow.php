<?php
namespace App\Tlog;

/**
 * Class SecGameStartFlow
 * 对局开始日志
 * @package app\datalog
 */
class GameStartFlow extends TLog
{
    
    protected $primary = 'SecGameStartFlow';
    
    //对局唯一ID
    public $BattleID = 0;
    
    public $RoundType = 0;
    
    public $Lives;//生命值
    
    public $Gold;
    
    public $Diamond;
    
    public $Plus3Count;
    
    public $BoomCount;
    
    public $PreRocketCount;
    
    public $SingleShotCount;
    
    public $RocketRowCount;
    
    public $RocketColCount;
    
    public $GunCount;
    
    public $SwitchCount;
    
    /**
     * 客户端提交
     * @var
     */
    public $RoundStepCount;
    public $RoundID;
    public $RoundTarget;
    
    public function scenarios()
    {
        return [
            'header' => [
                'gameSvrId',
                'clientTime',
                'vGameAppid',
                'vopenid',
                'platId',
                'areaId',
                'zoneId'
            ],
            'body' => [
                'BattleID',
                'RoundType',
                'Lives',
                'Gold',
                'Diamond' ,
                'Plus3Count',
                'BoomCount' ,
                'PreRocketCount' ,
                'SingleShotCount',
                'RocketRowCount',
                'RocketColCount',
                'GunCount',
                'SwitchCount',
                
                'RoundStepCount',
                'RoundID',
                'RoundTarget'
            ],
            'tail' => [],
        ];
    }
    
    
}