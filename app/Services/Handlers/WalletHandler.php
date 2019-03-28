<?php
namespace App\Services\Handlers;
use App\Constants\MoneyType;
use App\Models\Dao\UserInfoDao;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Core\ApplicationContext;
use Swoft\Core\RequestContext;


/**
 * @Bean()
 * Class WalletHandler
 * @package App\Services
 */
class WalletHandler implements PropertyHandlerInterface {


    const WALLET_CHANGED_NUM = 'walletChangedNum';

    private $moneyType = [
        MoneyType::GOLD   => 'gold_coin',
        MoneyType::SILVER => 'silver_coin',
        MoneyType::LIVE   => 'lives',
        MoneyType::REMAIN_STARS  => 'remainStars',
    ];

    public function handle($itemId, $num){

        if(!isset($this->moneyType[$itemId])){
            return false;
        }

        $changed_num = RequestContext::getContextDataByKey(self::WALLET_CHANGED_NUM);
        $field = $this->moneyType[$itemId];
        if(!isset($changed_num[$field])){
            $changed_num[$field] = $num;
        }else{
            $changed_num[$field] += $num;
        }

        RequestContext::setContextDataByKey(self::WALLET_CHANGED_NUM, $changed_num);

        return true;
    }

    /**
     * 检查item_id合法性
     * @param $itemId
     * @return bool
     */
    public function checkItemId($itemId) : bool{
        if(!isset($this->moneyType[$itemId])){
            return false;
        }
        return true;
    }

    /**
     * 请求结束时存入db
     * @return bool|\Swoft\Core\ResultInterface|\Swoft\Db\DbResult
     */
    public function save(){
        $changed_num = RequestContext::getContextDataByKey(self::WALLET_CHANGED_NUM);
        if(!$changed_num){
            return false;
        }
        return bean(UserInfoDao::class)->updateFields(globalInfo()->getUid(),$changed_num);
    }

}