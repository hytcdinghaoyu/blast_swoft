<?php
/**
 * This file is part of Swoft.
 *
 * @link    https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Controllers\Api;

use App\Constants\FlowActionConst;
use App\Constants\ItemID;
use App\Constants\Message;
use App\Constants\MoneyType;
use App\Constants\RedisKey;
use App\Controllers\CommonController;
use App\datalog\IapLog;
use App\Models\Dao\FamilyQuestDao;
use App\Models\Dao\ItemDao;
use App\Models\Dao\OrderDao;
use App\Models\Dao\UserInfoDao;
use App\Models\PromotionConfig;
use App\Models\Shop;
use App\Services\PayServiceInterface;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Utils\Utils;
use Swoft\Http\Server\Bean\Annotation\Controller;
use yii\helpers\ArrayHelper;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;


/**
 * 用户模块.
 * @Controller(prefix="/shop")
 */
class ShopController extends CommonController
{
    const GOODS_ID_RANDOM = 1018;

    /**
     * @RequestMapping(route="goodslist")
     * 商品列表
     */
    public function actionGoodsList(){
        $goodsList = Config::loadJson('payConfig');

        $weekDay = date('N');
        $isWeekend = ($weekDay == 6 || $weekDay == 7) ? true : false;

        $redisKey = sprintf(RedisKey::SHOP_BUY_COUNTS, globalInfo()->getUid());
        $buyCounts = bean('redis')->hGetAll($redisKey);

        foreach ($goodsList['gift'] as $k => &$giftGoods) {
            if(isset($giftGoods['limitWeekend']) && $giftGoods['limitWeekend'] == true && !$isWeekend){
                unset($goodsList['gift'][$k]);
            }

            if(isset($giftGoods['limitCounts'])){
                $giftGoods['limitCounts']['now'] = isset($buyCounts[$giftGoods['id']]) ? (int)$buyCounts[$giftGoods['id']] : 0;
            }
        }


        $goodsList['gift'] = array_values($goodsList['gift']);
        return $this->returnData(
            $goodsList
        );
    }

    /**
     * @RequestMapping(route="promotiongoods")
     * 获取限时促销礼包
     */
    public function actionPromotionGoods(){
        $productList = Config::loadJson('payPromotion');
        $itemPackList = ArrayHelper::index($productList['list'],'id');

        $promotionConfig = PromotionConfig::findOne(globalInfo()->getUid());
        $promotion = [];
        if(!$promotionConfig){
            return $this->returnData(['promotion' => $promotion]);
        }

        if(isset($itemPackList[$promotionConfig->productId])){
            $promotion = $itemPackList[$promotionConfig->productId];
            $promotion['remainTime'] = $promotionConfig->getTimeToLive();
        }

        return $this->returnData(
            [
                'promotion' => $promotion
            ]
        );
    }

    /**
     * 关前购买
     * @RequestMapping(route="prebuy")
     * @param $propId
     * @return array
     */
    public function actionPreBuy($propId)
    {
        // 判断道具是否存在
        $propList = $this->ifItemExist($propId);
        if ($propList == false) {
            return [
                'code' => '31201',
                'message' => 'No this item!',
                'itemId' => $propId
            ];
        }

        // 判断钱是否够买
        $globalInfo = $this->ifCoinNotEnough($propList [1], $propList [2]);
        if ($globalInfo == false) {
            return [
                'code' => '31202',
                'message' => "Don't have enough $propList[2]!",
                'itemId' => $propId
            ];
        }
        // 扣钱
        bean(PropertyService::class)->handleOne($propList [2], -$propList [1], FlowActionConst::DETAIL_SHOP_RPE_BUY,$propId);


        // 增加道具
        bean(PropertyService::class)->handleOne($propList [0], $propList [3], FlowActionConst::ACTION_ITME_SHOP_PREBUY,$propId);

        return [
            'code' => 1,
            'itemId' => $propId
        ];
    }

    /**
     * @RequestMapping(route="buy")
     * 商店购买
     * @param $propId
     * @param $place integer 购买的地方，关卡，挖地，大地图
     * @param $count integer V2版本道具采用阶梯价格，根据客户端传来的count选用不同的价格
     * @param $randomItemId
     * @return array
     */
    public function actionBuy($propId, $place, $count = 1, $randomItemId = 0)
    {

        // 判断道具是否存在
        $propList = $this->ifItemExist($propId);
        if ($propList == false) {
            return [
                'code' => '31201',
                'message' => 'No this item!',
                'itemId' => $propId
            ];
        }
        if (!in_array($place, [
            FlowActionConst::DETAIL_SHOP_BUY_WORLD,
            FlowActionConst::DETAIL_SHOP_BUY_LEVEL,
            FlowActionConst::DETAIL_SHOP_BUY_DIG,
            FlowActionConst::DETAIL_SHOP_BUY_TOWER_WORLD,
            FlowActionConst::DETAIL_SHOP_BUY_TOWER_BATTLE,
            FlowActionConst::DETAIL_SHOP_BUY_OL_WORLD,
            FlowActionConst::DETAIL_SHOP_BUY_OL_BATTLE,
            FlowActionConst::DETAIL_SHOP_BUY_4_STAR,
            FlowActionConst::DETAIL_SHOP_BUY_TOWER_SPECIAL_BATTLE,
            FlowActionConst::DETAIL_SHOP_BUY_TOWER_SPECIAL_WORLD,
            FlowActionConst::DETAIL_SHOP_BUY_MT_LEAGUE_WORLD,
            FlowActionConst::DETAIL_SHOP_BUY_MT_LEAGUE_BATTLE
        ])
        ) {
            return [
                'code' => '31203',
                'message' => 'Unknown place parameter!',
            ];
        }
        //处理阶梯，使其合法
        if (is_array($propList [1])) {
            $count = $count >= count($propList [1]) ? (count($propList [1]) - 1) : ($count - 1);
            $globalInfo = $this->ifCoinNotEnough($propList [1], $propList [2], $count);
            $moneyDeduction = $propList [1][$count];
        } else {
            $globalInfo = $this->ifCoinNotEnough($propList [1], $propList [2]);
            $moneyDeduction = $propList [1];
        }
        // 判断钱是否够买
        if ($globalInfo == false) {
            return [
                'code' => '31202',
                'message' => "Don't have enough $propList[2]!",
                'itemId' => $propId
            ];
        }
        // 扣钱
        bean(PropertyService::class)->handleOne($propList [2], -$moneyDeduction, $place,$propId);

        // 增加道具 加五步则不加
        if ($propId == static::GOODS_ID_RANDOM) {
            $isValid = bean(ItemDao::class)->isValidDbItem($randomItemId);
            if (!$isValid) {
                return [
                    'code' => '31204',
                    'message' => "Don't have this item id!",
                    'itemId' => $randomItemId
                ];
            }
            bean(PropertyService::class)->handleOne($randomItemId, $propList [3], FlowActionConst::ACTION_ITME_SHOP_BUY,$propId);
        } else {

            bean(PropertyService::class)->handleOne($propList [0], $propList [3], FlowActionConst::ACTION_ITME_SHOP_BUY,$propId);

        }

        return [
            'code' => 1,
            'itemId' => $propId,
        ];
    }

    /**
     * @RequestMapping(route="buygoods")
     * 商店用钻石购买礼包
     * @param $productId
     * @return array
     */
    public function actionBuyGoods($productId){
        $productList = Config::loadJson('payConfig');
        $itemPackList = ArrayHelper::index($productList['gift'],'id');

        if(!isset($itemPackList[$productId])){
            return $this->returnError(Message::INVALID_PRODUCT_ID);
        }

        $itemPack = $itemPackList[$productId];
        if($itemPack['priceType'] != 'coin'){
            return $this->returnError(Message::INVALID_PRODUCT_ID);
        }

        $costDiamond = $itemPack['price'];
        //余额是否充足
        $payService = bean(PayServiceInterface::class);
        $balance = $payService->getBalance();
        if($balance < $costDiamond){
            return $this->returnError(Message::DIAMOND_NOT_ENOUGH);
        }

        //扣除余额

        $billno = globalInfo()->getThirdId(). '_' . date('YmdHi') . '_' . Utils::mtime();
        $payRet = $payService->payMoney($costDiamond);

        //记录订单
        $orderStatus = 0;


        //本次成功和上次操作成功都进行发货
        if($payRet == true){
            $itemList = $itemPack['reward'];
            bean(PropertyService::class)->handleBatch($itemList,FlowActionConst::SHOP_BUY_GOODS,'');
            $orderStatus = OrderDao::STATUS_SUCCESS;

        }else{
            //操作失败
            return $this->returnError($payService->getErrorCode());
        }
        //记录订单
         OrderDao::createOne(globalInfo()->getUid(),OrderDao::TYPE_DIAMOND_BUY_GOODS,$billno,$productId,$orderStatus);


        return $this->returnData(
            [
                'reward' => $itemList
            ]
        );
    }


    /**
     * @RequestMapping(route="gold")
     * 现金购买金币，礼包等
     * @param $item
     * @param array $log
     * @return array
     */
    public function actionGold($item, $log = [])
    {
//        if (isset($log['order_id'])) {
//            $iapOrder = new IapOrder();
//            if ($iapOrder->checkExist($log['order_id'])) {
//                return [
//                    'code' => OrderMsg::ORDER_EXISTS,
//                    'message' => OrderMsg::showMessage(OrderMsg::ORDER_EXISTS),
//                ];
//            }
//        }

        $itemList = Config::loadJson('goldShop');
        if (isset($log['activityName'])) {
            Shop::filterActiveItem($itemList, $item, $log['activityName']);
        } else {
            /**
             * 为了兼容老代码
             */
            Shop::filterActiveItem($itemList, $item);
        }
        if (!isset ($itemList [$item])) {
            return [
                'code' => '31201',
                'message' => "No this item!"
            ];
        }

        $globalInfo = globalInfo();
        $incrArr = [
            'gold',
            'silver',
            'live'
        ];
        $mapArr = ['gold'=>MoneyType::GOLD,'silver'=>MoneyType::SILVER,'live'=>MoneyType::LIVE];
        foreach ($itemList [$item] ['package'] as $itemId => $number) {
            if (in_array($itemId, $incrArr)) {
                bean(PropertyService::class)->handleBatch([$mapArr[$itemId] => $number],FlowActionConst::DETAIL_SHOP_GOLD, $item);
            } else {
                bean(PropertyService::class)->handleBatch([$itemId => $number],FlowActionConst::ACTION_ITME_SHOP_GOLD, $item);

            }
        }

        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        $level = $userInfo->getNewLevel();

        // mysql 日志信息
        $mysqlLog = [
            "type" => "business",
            "map_version" => isset ($log ['map_version']) ? $log ['map_version'] : 0,
            "map_name" => isset ($log ['map_name']) ? $log ['map_name'] : 'unknown',
            "customer_id" => ( int )$globalInfo->getUid(),
            "channel" => $globalInfo->getChannel(),
            "item" => $item,
            "level" => $level,
            "e_time" => date('Y-m-d H:i:s'),
            "register_time" => ( int )$globalInfo->getCreatedAt(),
            "sceneName" => isset ($log ['sceneName']) ? $log ['sceneName'] : 'unknown',
            "sceneValue" => isset ($log ['sceneValue']) ? $log ['sceneValue'] : 0,
            "order_id" => isset ($log['order_id']) ? $log ['order_id'] : 0,
        ];

        //老版本字段不设置和新版设置为0,则记录消费日志
        if (!isset($log['skip_pay']) || (isset($log['skip_pay']) && $log['skip_pay'] == 0)) {
            $iapLog = new IapLog();
            $iapLog->mergeClientLog($mysqlLog);
            $iapLog->send();
            //Logger::log2file('iapLog', $mysqlLog);
        }


        //添加订单记录到mysql
        if (isset($log['order_id']) && isset($iapOrder)) {
//            $iapOrder->uid = $globalInfo->uid;
//            $iapOrder->order_id = $log['order_id'];
//            $iapOrder->item = $item;
//            $iapOrder->insert();
        }

        return [
            'code' => 1,
            'package' => $itemList [$item] ['package']
        ];
    }

    /**
     * @RequestMapping(route="changeitem")
     * 增删道具金币硬币
     * @param array [itemId=>num]
     */
    public function actionChangeItem($items, $detail = '')
    {

        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        foreach ($items as $kItemId => $vNum) {
            if($vNum > 0){
                return $this->returnError(Message::SYSTEM_ERROR_PARAM);
            }

            if ($vNum < 0) {
                if (($kItemId == 1279) && ($userInfo->getGoldCoin() + $vNum < 0)) {
                    return [
                        "code" => 51001,
                        "message" => "the user account balance is insufficient!"
                    ];
                }
                if (($kItemId == 1280) && ($userInfo->getSilverCoin() + $vNum < 0)) {
                    return [
                        "code" => 51001,
                        "message" => "the user account balance is insufficient!"
                    ];
                }
            }

            //记录城建questId
            if (strpos($detail, ":")) {
                list($flowAction, $questId) = explode(":", $detail);
                if ($flowAction == 'quick_build') {
                    bean(PropertyService::class)->handleOne($kItemId, $vNum, FlowActionConst::ACTION_QUICK_BUILD, $questId);
                } elseif ($flowAction == 'buy_build') {
                    bean(PropertyService::class)->handleOne($kItemId, $vNum, FlowActionConst::ACTION_QUICK_BUILD, $questId);
                } elseif ($flowAction == FamilyQuestDao::KEY_CHAPTER_REWARD) {
                    $chapterId = $questId;
                    FamilyQuestDao::setChapterReward($globalInfo->getUuid(), $chapterId);
                }
            }
        }
        bean(PropertyService::class)->handleBatch($items, FlowActionConst::DETAIL_CLIENT_OPTION, $detail);

        return [
            'code' => 1
        ];
    }

    /**
     *  传入道具id 返回道具id列表
     * @param $propId
     * @return mixed false 不存在, array $list 配置项
     */
    public function ifItemExist($propId)
    {
        $propList = Config::loadJson('shop');

        if (isset ($propList [$propId])) {
            return $propList [$propId];
        }
        return false;
    }

    /**
     * 传入 道具金额 and 道具类型 返回 数据库操作类
     * @param $propNum , $propType 道具类型
     * @return mixed false 余额不足, $globalInfo
     */
    protected function ifCoinNotEnough($propNum, $propType, $count = null)
    {
        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        if ($propType == MoneyType::SILVER) {
            $userSum = $userInfo->getSilverCoin();
        } else {
            $payService = bean(PayServiceInterface::class);
            $userSum = $payService->getBalance();
        }
        if ($count === null) {
            $money = $propNum;
        } else {
            $money = $propNum[$count];
        }
        if ($userSum >= $money) {
            return $globalInfo;
        }
        return false;
    }
}