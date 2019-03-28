<?php
namespace App\Services;
use App\Constants\FlowAction;
use App\Constants\MoneyType;
use App\Models\Dao\UserInfoDao;
use Swoft\Bean\Annotation\Bean;

/**
 * Class RovioPayService
 * @Bean("rovio")
 * @package App\Services
 */
class RovioPayService implements PayServiceInterface{
    
    public function getBalance(): int
    {
        $userInfo = bean(UserInfoDao::class)->findOneByUid(globalInfo()->getUid());
        return $userInfo->getGoldCoin();
    }
    
    public function payMoney($amount): bool
    {
        bean(PropertyService::class)->handleOne(MoneyType::GOLD, -$amount, FlowAction::DETAIL_SHOP_PAY);
        return true;
    }
    
    public function presentMoney($amount): bool
    {
        bean(PropertyService::class)->handleOne(MoneyType::GOLD, $amount, FlowAction::ACTION_REWARD_SHARE);
        return true;
    }
    
}