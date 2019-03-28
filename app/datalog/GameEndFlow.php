<?php

namespace App\datalog;

use  App\Constants\BlockID;
use  App\Constants\ItemID;
use App\Models\Dao\UserInfoDao;


/**
 * Class SecGameEndFlow
 * 对局结束日志
 * @package app\datalog
 */
class GameEndFlow extends DataLog
{
    
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
    
    
    public static function newFlow(array $itemsUse, array $itemsBuy, array $eliminated){
    
        $userInfo = bean(UserInfoDao::class)->findOneByUid(globalInfo()->getUid());
    
        $log = new self();
        $log->RoundLives = $userInfo->getLives();
        
        //道具使用
        $log->RoundBoomUse = $itemsUse[ItemID::BOMB] ?? 0;
        $log->RoundGunUse  =  $itemsUse[ItemID::GUN] ?? 0;
        $RocketColCountUse = $itemsUse[ItemID::ROCKET_COLUMN] ?? 0;
        $RocketRowCountUse = $itemsUse[ItemID::ROCKET_ROW] ?? 0;
        $log->RoundRocketUse = $RocketColCountUse + $RocketRowCountUse;
        $log->RoundSwitchUse = $itemsUse[ItemID::SWITCH] ?? 0;
        $log->RoundSingleUse = $itemsUse[ItemID::SINGLE] ?? 0;
        $log->RoundItemPlus3Use = $itemsUse[ItemID::PLUS_3_STEP] ?? 0;
    
        //道具购买
        $log->RoundBoomBuy = $itemsBuy[ItemID::BOMB] ?? 0;
        $log->RoundGunBuy  =  $itemsBuy[ItemID::GUN] ?? 0;
        $RocketColCountBuy = $itemsBuy[ItemID::ROCKET_COLUMN] ?? 0;
        $RocketRowCountBuy = $itemsBuy[ItemID::ROCKET_ROW] ?? 0;
        $log->RoundRocketBuy = $RocketColCountBuy + $RocketRowCountBuy;
        $log->RoundSwitchBuy = $itemsBuy[ItemID::SWITCH] ?? 0;
        $log->RoundSingleBuy = $itemsBuy[ItemID::SINGLE] ?? 0;
        $log->RoundItemPlus3Buy = $itemsBuy[ItemID::PLUS_3_STEP] ?? 0;
    
        //元素消除
        $log->RoundCubeRedKillCount = $eliminated[BlockID::BLOCK_TYPE_NORMAL_RED] ?? 0;
        $log->RoundCubeYellowKillCount = $eliminated[BlockID::BLOCK_TYPE_NORMAL_YELLOW] ?? 0;
        $log->RoundCubeBlueKillCount = $eliminated[BlockID::BLOCK_TYPE_NORMAL_BLUE] ?? 0;
        $log->RoundCubeWhiteKillCount = $eliminated[BlockID::BLOCK_TYPE_NORMAL_WHITE] ?? 0;
        $log->RoundCubeBlackKillCount = $eliminated[BlockID::BLOCK_TYPE_NORMAL_BLACK] ?? 0;
        $log->RoundCubePinkKillCount = $eliminated[BlockID::BLOCK_TYPE_NORMAL_PINK] ?? 0;
        
    
        return $log;
    }
    
    
    
}