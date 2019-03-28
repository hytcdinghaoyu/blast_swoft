<?php

namespace App\Controllers\Api;


use App\Constants\Message;
use App\Constants\MsdkMessage;
use App\Controllers\CommonController;
use App\Models\Dao\UserInfoDao;
use App\Services\PayServiceInterface;
use App\Services\ReportDataService;
use App\Services\TencentPayService;
use App\Utils\Config;
use yii\helpers\ArrayHelper;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\Controller;


/**
 * 支付.
 * @Controller(prefix="/pay")
 */
class PayController extends CommonController
{
    
    /**
     * @RequestMapping(route="getbalance")
     * 查询游戏币余额
     * 客户端完成充值之后校验米大师账户是否到账
     */
    public function actionGetBalance($productId = '', $billNo = ''){
        
        $productList = Config::loadJson('payConfig');
        $itemPackList = ArrayHelper::index($productList['normal'],'id');
    
        if(!isset($itemPackList[$productId])){
            return $this->returnError(Message::INVALID_PRODUCT_ID);
        }
    
        $payService = bean(PayServiceInterface::class);
        $balance = $payService->getBalance();
        
        $saveAmt = $payService->getAmt();
        $isChargeSuccess = false;
        //根据返回的累计充值金额判断是否充值到账
        
        $uid = globalInfo()->getUid();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($uid);
        if($saveAmt > $userInfo->getSaveAmt()){
            $isChargeSuccess = true;

            //上报充值数据
            $oneSaveAmt = $saveAmt - $userInfo->getSaveAmt();
            ReportDataService::ReportPay(globalInfo()->getThirdId(),$oneSaveAmt,$saveAmt);

            //更新累计充值金额
            bean(UserInfoDao::class)->updateAll($uid, ['save_amt' => $saveAmt]);
            //赠送大礼包中额外赠送的游戏里
            $pack = $itemPackList[$productId];
            if(isset($pack['send'])){
                $presentMoney = array_sum($pack['send']);
                $ret = $payService->presentMoney($presentMoney);
                if($ret == true){
                    $balance += $presentMoney;
                }
            }
//            \Yii::$app->globalInfo->getUserInfo()->gold_coin += $balance;
    
        }
        
        
        
        return $this->returnData(
            [
                'balance' => $balance,
                'chargeSuccess' => $isChargeSuccess
            ]
        );
        
    }
    
    
    /**
     * 直购下单
     * @RequestMapping(route="buygoods")
     * @param $productId
     * @return array
     */
    public function actionBuyGoods($productId){
        $productList = Config::loadJson('payConfig');
        $promotionList = Config::loadJson('payPromotion');
        $itemPackList = ArrayHelper::index($productList['gift'],'id');
        $promotionPackList = ArrayHelper::index($promotionList['list'],'id');
    
        $itemPackList = ArrayHelper::merge($itemPackList, $promotionPackList);
        
        if(!isset($itemPackList[$productId])){
            return $this->returnError(Message::INVALID_PRODUCT_ID);
        }
        
        $product = $itemPackList[$productId];
//        $payItem = TencentPayService::ItemArrToStr($product['reward']);
        $payItem = sprintf("%s*%d*%d",$productId, $product['price'] * 10, 1);
        $name = $product['name'] ?? $productId;
        $desc = $product['desc'] ?? $productId;
        $goodsMeta = sprintf("%s*%s", $name, $desc);
    
        $payService = bean(PayServiceInterface::class);
        $ret = $payService->apiBuyGoods($payItem, $goodsMeta, $productId);
    
        if($ret['ret'] != MsdkMessage::SUCCESS){
            return [
                'code' => $ret['ret'],
                'msg' => $ret['msg']
            ];
        }
        
        return $this->returnData(
            [
                'tokenUrl' => $ret['url_params']
            ]
        );
        
    }
}