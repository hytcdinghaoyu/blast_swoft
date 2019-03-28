<?php
namespace App\Tlog;


/**
 * Class SecGameEndFlow
 * 对局结束日志
 * @package app\datalog
 */
class GameEndFlow extends TLog
{
    
    protected $primary = 'SecGameEndFlow';
    
    //对局唯一ID
    public $BattleID = 0;
    
    //服务端校验作弊情况，0为通过
    public $Result = 0;

    //本局结束方式，0为副本正常结束，1为挑战失败离开副本，2为玩家中途离开，3为系统踢出副本
    public $RoundEndType = 0;
    
    //客户端统计的通关时间，单位毫秒（不包含暂停游戏的时间）
    public $RoundTimeUse = 0;
    
    //本局战斗结果评价星级（1，2，3）
    public $RoundRate = 1;
    
    //当局获得金币数量
    public $RoundIncomeGold = 0;
    
    //当局获得道具流水，格式为|道具ID1，道具ID2，道具ID3，道具ID4……|
    public $RoundItemGet = 'NULL';
    
    //本局结束后体力剩余值
    public $RoundLives = 0;
    
    //本局游戏类型
    public $RoundType = 1;
    
    //本局累计使用火箭数量
    public $RoundRocketUse = 0;
    
    //本局累计购买火箭数量
    public $RoundRocketBuy = 0;
    
    //本局火箭累计消除元素数量（不含连锁反应）
    public $RoundRocketErase = 0;
    
    //本局累计使用道具枪数量
    public $RoundGunUse = 0;
    
    public $RoundGunBuy = 0;
    
    public $RoundGunErase = 0;
    
    public $RoundBoomUse = 0;
    
    public $RoundBoomBuy = 0;
    
    public $RoundBoomErase = 0;
    
    public $RoundItemPlus3Use = 0;
    
    public $RoundItemPlus3Buy = 0;
    
    public $RoundItemPlus3Erase = 0;
    
    public $RoundSingleUse = 0;
    
    public $RoundSingleBuy = 0;
    
    public $RoundSingleErase = 0;
    
    public $RoundSwitchUse = 0;
    
    public $RoundSwitchBuy = 0;
    
    public $RoundSwitchErase = 0;
    
    public $RoundStepUse = 0;
    
    public $RoundStepBuy = 0;
    
    //消除元素数量
    public $RoundCubeRedKillCount = 0;
    public $RoundCubeYellowKillCount = 0;
    public $RoundCubeBlueKillCount = 0;
    public $RoundCubeWhiteKillCount = 0;
    public $RoundCubeBlackKillCount = 0;
    public $RoundCubePinkKillCount = 0;
    
    public $RoundCDMax = 0;
    public $RoundCDMin = 0;
    
    
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
                'ClientVersion',
                'clientIp',
                'Result' ,
                'RoundEndType',
                'RoundTimeUse',
                'RoundRate',
                'RoundIncomeGold',
                'RoundItemGet',
                'RoundLives',
                'RoundType',
                
                'RoundRocketUse',
                'RoundRocketBuy',
                'RoundRocketErase',
                'RoundGunUse',
                'RoundGunBuy',
                'RoundGunErase',
                'RoundBoomUse',
                'RoundBoomBuy',
                'RoundBoomErase',
                'RoundItemPlus3Use',
                'RoundItemPlus3Buy',
                'RoundItemPlus3Erase',
                'RoundSingleUse',
                'RoundSingleBuy',
                'RoundSingleErase',
                'RoundSwitchUse',
                'RoundSwitchBuy',
                'RoundSwitchErase',
                'RoundStepUse',
                'RoundStepBuy',
                
                'RoundCubeRedKillCount',
                'RoundCubeYellowKillCount',
                'RoundCubeBlueKillCount',
                'RoundCubeWhiteKillCount',
                'RoundCubeBlackKillCount',
                'RoundCubePinkKillCount',
                'RoundCDMax',
                'RoundCDMin'
            ],
            'tail' => [],
        ];
    }
    
    
    
}