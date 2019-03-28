<?php

namespace App\Services;

use App\Base\SnsNetwork;
use App\Base\SnsSigCheck;
use App\Constants\ThirdPlatform;
use App\Models\TencentAccess;
use App\Constants\MsdkMessage as Message;
use App\Utils\Utils;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Core\RequestContext;


/**
 * @Bean("tencent")
 * Class TencentPayService
 * @package App\Services
 */
class TencentPayService implements PayServiceInterface
{
    
    
    const OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY = 1801;// 参数格式错误
    const OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID = 1802;// 参数格式错误
    const OPENAPI_ERROR_RESPONSE_DATA_INVALID = 1803;// 返回包格式错误
    const OPENAPI_ERROR_CURL = 1900;// 网络错误, 偏移量1900, 详见 http://curl.haxx.se/libcurl/c/libcurl-errors.html
    
    const URL_GET_BALANCE = '/v3/r/mpay/get_balance_m';//查余额
    const URL_PAY_MONEY = '/v3/r/mpay/pay_m';//消耗游戏币
    const URL_CANCEL_PAY_MONEY = '/v3/r/mpay/cancel_pay_m';//回滚消耗游戏币
    const URL_PRESENT_MONEY = '/v3/r/mpay/present_m';//赠送游戏币
    const URL_BUY_GOODS = '/v3/r/mpay/buy_goods_m';
    
    const ERROR_CODE = 'midasErrorCode';
    const SAVE_AMT = 'saveAmt';
    
    public $format = 'json';
    
    public $protocol = 'https';
    
    private $commonParams = [];
    
    private $cookieParams = [];
    
    private $appId = '';
    
    private $appKey = '';
    
    private $signKey = '';
    
    private $url = '';
    
    private function initData()
    {
        $globalInfo = globalInfo();
        
        //通用cookie
        $this->cookieParams['session_id'] = $this->getSessionId($globalInfo->getThirdBind());
        $this->cookieParams['session_type'] = $this->getSessionType($globalInfo->getThirdBind());
        
        //ios,android配置不同
        $platform = $globalInfo->getPlatform();
        
        $config = config("tencentApi.$platform");
        if(!$config){
            throw new \Exception('get tencentApi appid wrong, platform invalid');
        }
        
        $this->commonParams['appid'] = $this->appId = $config['appId'];
        $this->appKey = $config['appKey'];
        $this->signKey = $config['signKey'];
        $this->url = $config['url'];
        
        //腾讯登录态
        $tentAccess = TencentAccess::findOne($globalInfo->getThirdId());
        if(!$tentAccess){
            throw new \Exception("tencentAccess lost openid:" . $globalInfo->getThirdId());
        }
        
        $this->commonParams['openid'] = $globalInfo->getThirdId();
        $this->commonParams['openkey'] = isset($tentAccess->openKey) ? $tentAccess->openKey: '';
        $this->commonParams['pf'] = isset($tentAccess->pf) ? $tentAccess->pf : '';
        $this->commonParams['pfkey'] = isset($tentAccess->pfKey) ? $tentAccess->pfKey : '';
        
        //其他通用参数
        $this->commonParams['format'] = $this->format;
        $this->commonParams['ts'] = time();
        $this->commonParams['zoneid'] = 1;
        
    }
    
    private function setErrorCode(int $code){
        RequestContext::setContextDataByKey(self::ERROR_CODE, $code);
    }
    
    public function getErrorCode(){
        return RequestContext::getContextDataByKey(self::ERROR_CODE);
    }
    
    private function setAmt(int $amt){
        RequestContext::setContextDataByKey(self::SAVE_AMT, $amt);
    }
    
    public function getAmt(){
        return RequestContext::getContextDataByKey(self::SAVE_AMT);
    }
    
    /**
     * 发起api请求
     * @param        $url_path
     * @param        $params
     * @param        $cookie
     * @param string $method
     * @param        $protocol
     */
    private function api($url_path, $params, $method = 'GET', $protocol = 'https'){
        
        //cookie
        $cookie = $this->cookieParams;
        $cookie['org_loc'] = $url_path;
        // 生成签名
        $secret = $this->appKey . '&';
        $params['sig'] = SnsSigCheck::makeSig( $method, $url_path, $params, $secret);
        
        //改用非阻塞的swoole client
        $ret = SnsNetwork::makeCoRequest($this->url, $url_path, $params, $cookie, $method, $protocol);
        
        if (false === $ret['result'])
        {
            return array(
                'ret' => self::OPENAPI_ERROR_CURL + $ret['errno'],
                'msg' => $ret['msg'],
            );
        }
        
        $result_array = json_decode($ret['msg'], true);
        
        // 远程返回的不是 json 格式, 说明返回包有问题
        if (is_null($result_array)) {
            $result_array = array(
                'ret' => self::OPENAPI_ERROR_RESPONSE_DATA_INVALID,
                'msg' => $ret['msg']
            );
        }
        
        //返回失败则记录log
        if($result_array['ret'] != Message::SUCCESS){
            $cookieJson = json_encode($cookie);
            $commonJson = json_encode($params);
            App::error("request openApi Error, code:{$result_array['ret']}, msg:{$result_array['msg']}, params:$commonJson, cookie:$cookieJson, raw:" . $ret['msg']);
        }
        
        return $result_array;
    }
    
    /**
     * 查询游戏币余额
     * @return array|mixed
     */
    public function getBalance() : int{
        $this->initData();
        $params = $this->commonParams;
        $ret = $this->api(self::URL_GET_BALANCE, $params);
        
        if(!isset($ret['balance'])){
            $this->setErrorCode($ret['ret']);
        }
        
        if(isset($ret['save_amt'])){
            $this->setAmt($ret['save_amt']);
        }
        
        return isset($ret['balance']) ? $ret['balance'] : 0;
    }
    
    /**
     * 消耗游戏币
     */
    public function payMoney($amount, $billno = '') : bool{
        $this->initData();
        $params = $this->commonParams;
        $params['billno'] = $billno ? $billno : self::createOrderId();
        $params['amt'] = abs($amount);
        
        // 发起请求
        $ret = $this->api(self::URL_PAY_MONEY, $params);
        
        //订单未确认，重试三次
        if($ret['ret'] == Message::ORDER_NOT_CONFIRMED){
            $ret = $this->retryPayMoney($params['billno'], $amount);
        }
        
        //重试三次依然未确认则记录log，人工稽核
        if($ret['ret'] == Message::ORDER_NOT_CONFIRMED){
            App::warning("pay money failed, billno:{$params['billno']}");
        }
        
        if($ret['ret'] === Message::SUCCESS || $ret['ret'] == Message::LAST_SUCCESS){
            $return = true;
        }else{
            $return = false;
            $this->setErrorCode($ret['ret']);
        }
    
        return $return;
    }
    
    private function retryPayMoney($billno, $amount, $retryTimes = 3){
        $params = $this->commonParams;
        $params['billno'] = $billno;
        $params['amt'] = abs($amount);
        
        // 发起请求
        $ret = $this->api(self::URL_PAY_MONEY, $params);
        
        //订单未确认，重试三次
        if($ret['ret'] == Message::ORDER_NOT_CONFIRMED && $retryTimes > 1){
            $ret = $this->retryPayMoney($billno, $amount, --$retryTimes);
        }
        
        return $ret;
    }
    
    /**
     * 赠送游戏币
     */
    public function presentMoney($amount) : bool{
        $this->initData();
        $params = $this->commonParams;
        $params['billno'] = self::createOrderId();
        $params['presenttimes'] = abs($amount);
        
        // 发起请求
        $ret = $this->api(self::URL_PRESENT_MONEY, $params);
    
        if($ret['ret'] === Message::SUCCESS){
            $return = true;
        }else{
            $return = false;
            $this->setErrorCode($ret['ret']);
        }
    
        return $return;
    }
    
    public static function createOrderId(){
        return  globalInfo()->getThirdId() . '_' . date('YmdHi') . '_' . Utils::mtime();
    }
    
    /**
     * 直购道具
     */
    public function apiBuyGoods($payItem, $goodsMeta, $productId){
        $this->initData();
        
        $params = $this->commonParams;
        
        //道具直购和查询余额所用appKey不同
        $this->appKey = $this->signKey;
        $params['appid'] = 1450017686;
        $params['payitem'] = $payItem;
        $params['goodsmeta'] = $goodsMeta;
        $params['appmode'] = 1;
        $params['app_metadata'] = $productId;
        $ret = $this->api(self::URL_BUY_GOODS, $params);
        return $ret;
    }
    
    //礼包，数组转字符串
    public static function ItemArrToStr($arr){
        $itemArr = [];
        foreach ($arr as $itemId => $num) {
            $itemArr[] = sprintf("%s*%d*%d",$itemId, 1, $num);
        }
        return implode(';', $itemArr);
    }
    
    public static function ItemStrToArr($str){
    
    }
    
    private function getSessionId($thirdBind){
        $map = [
            ThirdPlatform::QQ => 'openid',
            ThirdPlatform::WX => 'hy_gameid',
            ThirdPlatform::GUEST => 'hy_gameid'
        ];
        
        return isset($map[$thirdBind]) ? $map[$thirdBind] : '';
    }
    
    private function getSessionType($thirdBind){
        
        $map = [
            ThirdPlatform::QQ => 'kp_actoken',
            ThirdPlatform::WX => 'wc_actoken',
            ThirdPlatform::GUEST => 'st_dummy'
        ];
        
        return isset($map[$thirdBind]) ? $map[$thirdBind] : '';
    }
    
    
    
}