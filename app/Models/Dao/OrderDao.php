<?php
namespace App\Models\Dao;

use App\Models\Entity\Order;
use Swoft\Bean\Annotation\Bean;

/**
 *
 * @Bean()
 */
class OrderDao
{
    const STATUS_SUCCESS = 1;
    const STATUS_NOT_COMPLETE = 0;

    const TYPE_CHARGE_DIAMOND = 1;//rmb买游戏币
    const TYPE_DIAMOND_BUY_GOODS = 2;//消耗游戏币买道具
    const TYPE_PRESENT_DIAMOND = 3;//活动系统赠送
    const TYPE_MONEY_BUY_GOODS = 4;//直接购买道具礼包

    public static function createOne($uid,$type,$billno,$productId,$status){
        $order = new Order();
        $order->setUid($uid);
        $order->setType($type);
        $order->setBillno($billno);
        $order->setProductId($productId);
        $order->setCreatedAt(time());
        $order->setUpdatedAt(time());
        $order->setStatus($status);
        $order->save()->getResult();
    }
}