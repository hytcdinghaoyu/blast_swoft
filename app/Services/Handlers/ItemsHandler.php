<?php
namespace App\Services\Handlers;
use App\Constants\ItemID;
use App\Models\Dao\ItemDao;
use App\Models\Dao\UserInfoDao;
use Swoft\Bean\Annotation\Bean;
use Swoft\Core\RequestContext;


/**
 * @Bean()
 * Class WalletHandler
 * @package App\Services
 */
class ItemsHandler implements PropertyHandlerInterface {


    const ITEM_CHANGED_NUM = 'itemChangedNum';


    public function handle($itemId, $num){

        if(!ItemID::isValidValue($itemId)){
            return false;
        }

        $changed_num = RequestContext::getContextDataByKey(self::ITEM_CHANGED_NUM);
        if(!isset($changed_num[$itemId])){
            $changed_num[$itemId] = $num;
        }else{
            $changed_num[$itemId] += $num;
        }

        RequestContext::setContextDataByKey(self::ITEM_CHANGED_NUM, $changed_num);

        return true;
    }

    /**
     * 检查item_id合法性
     * @param $itemId
     * @return bool
     */
    public function checkItemId($itemId) : bool{
        if(!ItemID::isValidValue($itemId)){
            return false;
        }
        return true;
    }

    /**
     * 请求结束时存入db
     * @return bool|\Swoft\Core\ResultInterface|\Swoft\Db\DbResult
     */
    public function save(){
        $changed_num = RequestContext::getContextDataByKey(self::ITEM_CHANGED_NUM);
        if(!$changed_num){
            return false;
        }
        return bean(ItemDao::class)->updateNumber(globalInfo()->getUid(),$changed_num);
    }

}