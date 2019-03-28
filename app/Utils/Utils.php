<?php

namespace App\Utils;

use App\Constants\RedisKey;
use App\Models\Entity\CenterUser;
use Swoft\App;
use yii\helpers\BaseInflector;
use yii\helpers\Json;

class Utils
{

    public static function nextMidnight()
    {
        return time() - time() % 86400 + 86400;
    }

    public static function randomName()
    {
        $list = Config::loadJson('names');
        return $list[array_rand($list)];
    }

    /**
     * 对比app版本号大小
     *
     * @param $v
     *
     * @return mixed By default, version_compare returns
     * -1 if the first version is lower than the second,
     * 0 if they are equal, and
     * 1 if the second is lower.
     */
    public static function compareWithAppVersion($v)
    {
        if (APPVERSION === 1) {
            $appVersion = '1.0.0';
        } else {
            $appVersionArr = explode('.', APPVERSION);
            array_pop($appVersionArr);
            $appVersion = implode('.', $appVersionArr);
        }
        return version_compare($appVersion, $v);
    }

    /**
     * 获取周一的时间点；
     *
     * @return false|int
     */
    public static function monday()
    {
        $nowTime = ServerTime::getTestTime();
        // 周一到周日分别为1-7
        $dayOfWeek = date('w', $nowTime);
        if (0 == $dayOfWeek) {
            $dayOfWeek = 7;
        }
        $mondayDeviation = $dayOfWeek - 1;
        $monday = strtotime("-{$mondayDeviation} day", $nowTime);
        $monday = strtotime(date('Y-m-d', $monday));
        return $monday;
    }

    /**
     * 获得当前的毫秒时间戳
     *
     * @return number
     */
    public static function mtime()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }


    public static function ApiRequestNew($server, $token, $action, $apiVersion = 'api', $uuid = null)
    {

        $t = time() * 10000;
        $post = [
            'actions' => [
                $t => $action
            ],
        ];
        if (!is_null($uuid)) {
            $post['uuid'] = $uuid;
        }
        $data = json_encode($post);

        $serverUrl = \Yii::$app->params['servers'][$server];
        $url = "http://" . $serverUrl . '/' . $apiVersion;
        $header = ['token:' . $token];
        if (YII_DEBUG) {
            $result = self::serverRequestNew($url, $data, $header);
        } else {
            $security = new Security();
            $data = $security->hash($data);
            $result = self::serverRequestNew($url, $data, $header);
            $result = $security->unHash($result);
        }
        return json_decode($result, true);
    }

    public static function ApiRequestPop($server, $token, $action, $apiVersion = 'api', $uuid = null)
    {

        $t = time() * 10000;
        $post = [
            'actions' => [
                $t => $action
            ],
        ];
        if (!is_null($uuid)) {
            $post['uuid'] = $uuid;
        }
        $data = json_encode($post);

        $serverUrl = \Yii::$app->params['servers'][$server];
        $url = "http://" . $serverUrl . '/' . $apiVersion;
        $header = ['token:' . $token];
        if (YII_DEBUG) {
            $result = self::serverRequestNew($url, $data, $header);
        } else {
            /**
             * 用token找到密钥
             */
            $yiiSecurity = \Yii::$app->getSecurity();
            $token = $yiiSecurity->unmaskToken($token);
            $secret_data = Json::decode($yiiSecurity->decryptByKey($token,
                \Yii::$app->globalInfo->secret));

            if (!isset($secret_data['secret'])) {
                $ret = [
                    'code'    => 20009,
                    'message' => 'token not found!',
                ];

                return $ret;
            }

            $secret = $secret_data['secret'];

            $security = new Security($secret);
            $data = $security->hash($data);
            $result = self::serverRequestNew($url, $data, $header);
            $result = $security->unHash($result);
        }
        return json_decode($result, true);
    }

    public static function serverRequestNew($url, $data, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 随机用户头像
     */
    public static function randIcon()
    {
        $icos = array("1", "2", "3", "4", "5");
        shuffle($icos); // 头像
        return $icos [1];
    }

    public static function guid()
    {
        if (function_exists('com_create_guid')) {
            return strstr(com_create_guid(), ['{' => '', '}' => '']);
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
            return $uuid;
        }
    }

    public static function appVersionToInt($appVersion)
    {
        return $appVersion == '1' ? $appVersion : str_replace('.', '', substr($appVersion, 0, 5));
    }

    /**
     * uid 格式为大区ID拼上redis的Inc number
     *
     * @return int
     */
    public static function createUid(): int
    {
        $zoneId = config('zoneId');
        $key = RedisKey::CURRENT_UID_INC;
        $redis = App::getBean('redis');
        $currentUidInc = $redis->get($key);
        //如果redis中没有记录，首先初始化
        if (!$currentUidInc) {
            $maxUid = CenterUser::query()->max('uid')->getResult();
            if ($maxUid === null) {//如果数据库中没有记录，使用随机数初始化
                $redis->set($key, rand(10000, 100000));
            } else {//如果数据库中有记录，用最大uid的Inc number初始化(去除uid的大区ID部分)
                //如果当前数据库中的uid不符合当前大区的Id，直接将其作为Inc number使用，否则取其Inc number
                $inc = ($zoneId == substr($maxUid, 0, strlen($zoneId))) ? substr($maxUid, strlen($zoneId)) : $maxUid;
                $redis->set($key, $inc);
            }
        }
        $uidInc = $redis->incrby($key, rand(1, 20));
        return (int)($zoneId . $uidInc);
    }

    public static function getRealIp()
    {
        return request()->getHeader('x-forwarded-for')[0] ?? request()->getServerParams()['remote_addr'];
    }

    public static function changeCamelArray($arr){
        if (!is_array($arr)){
            return $arr;
        }
        $res = [];
        foreach ($arr as $key=>$value){
            $newKey = BaseInflector::camel2id($key,"_");
            $res[$newKey] = $value;
        }
        return $res;
    }

    public static function formatArrayValue($arr){
        if (!is_array($arr)){
            return $arr;
        }
        $res = [];
        foreach ($arr as $key=>$value){
            if (is_array($value)){
                $res[$key] = self::formatArrayValue($value);
            }else{
                $res[$key] = preg_match("/^\d{1,15}$/",$value) ? (int)$value : $value;
            }
        }
        return $res;
    }
}