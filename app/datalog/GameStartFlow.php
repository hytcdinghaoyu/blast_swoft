<?php

namespace App\datalog;

use App\Constants\ItemID;
use App\Models\Entity\Item;
use Swoft\Db\Query;
use App\Models\Dao\UserInfoDao;
use yii\helpers\ArrayHelper;

/**
 * Class SecGameStartFlow
 * 对局开始日志
 * @package app\datalog
 */
class GameStartFlow extends DataLog
{
    
    //对局唯一ID
    public $BattleID = 0;
    
    public $RoundType = 1;
    
    public $Lives = 0;//生命值
    
    public $Gold = 0;
    
    public $Diamond = 0;
    
    public $Plus3Count = 0;
    
    public $BoomCount = 0;
    
    public $PreRocketCount = 0;
    
    public $SingleShotCount = 0;
    
    public $RocketRowCount = 0;
    
    public $RocketColCount = 0;
    
    public $GunCount = 0;
    
    public $SwitchCount = 0;
    
    /**
     * 客户端提交
     * @var
     */
    public $RoundStepCount = 0;
    public $RoundID = 0;
    public $RoundTarget = 'NULL';
    
    public function rules()
    {
        $rules = [
            [['BattleID'], 'integer']
        ];
        return ArrayHelper::merge(parent::rules(), $rules);
    }
    
    public static function newFlow($roundType){

        $globalInfo =globalInfo();
        $uid = $globalInfo->getUid();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        $log = new self();
        $log->RoundType = $roundType;
        $log->Lives = $userInfo->getLives();
        $log->Gold = $userInfo->getSilverCoin();
        $log->Diamond = $userInfo->getGoldCoin();
    
        $allItems = Query::table(Item::class)->condition(['uid'=>$uid])->get()->getResult();
        $itemsMap = ArrayHelper::map($allItems, 'item', 'number');
    
        $log->BoomCount = $itemsMap[ItemID::BOMB] ?? 0;
        $log->GunCount = $itemsMap[ItemID::GUN] ?? 0;
        $log->RocketColCount = $itemsMap[ItemID::ROCKET_COLUMN] ?? 0;
        $log->RocketRowCount = $itemsMap[ItemID::ROCKET_ROW] ?? 0;
        $log->SwitchCount = $itemsMap[ItemID::SWITCH] ?? 0;
        $log->SingleShotCount = $itemsMap[ItemID::SINGLE] ?? 0;
        $log->Plus3Count = $itemsMap[ItemID::PLUS_3_STEP] ?? 0;
        
        return $log;
    }
    
    
    
}