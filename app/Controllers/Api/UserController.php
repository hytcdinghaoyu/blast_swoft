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

use App\Constants\DotProbabilityConst;
use App\Constants\FlowActionConst;
use App\Constants\ItemID;
use App\Constants\Message;
use App\Constants\PunishType;
use App\Constants\RedisKey;
use App\Controllers\CommonController;
use App\datalog\CreateUserLog;
use App\datalog\LogAnti;
use App\datalog\LoginLog;
use App\Models\Dao\CenterUserDao;
use App\Models\Dao\ItemDao;
use App\Models\Dao\MyFriendsDao;
use App\Models\Dao\UserInfoDao;
use App\Models\PunishDetail;
use App\Models\Shop;
use App\Services\FriendLivesService;
use App\Services\HatchService;
use App\Services\PayServiceInterface;
use App\Services\PropertyService;
use App\Services\PunishService;
use App\Services\ReportDataService;
use App\Services\SignService;
use App\Utils\Config;;
use Swoft\App;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\HttpClient\Client;
use yii\helpers\ArrayHelper;


/**
 * 用户模块.
 * @Controller(prefix="/user")
 */
class UserController extends CommonController
{

    /**
     * 用户初始化
     * @RequestMapping(route="initialize")
     * @param int $startFromPlatform 是否从平台启动
     * @return array
     */
    public function actionInitialize($startFromPlatform = 0)
    {
        
        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        bean(UserInfoDao::class)->setDailyActive();


        //设置平台特权
        if ($startFromPlatform) {
            UserInfoDao::activePlatformVip();
        }
        $isPlatformVip = UserInfoDao::isPlatformVip();


        /**
         * 更新最后登录时间
         */
        $userInfo->setLastLoginAt(time());


        $allItemsArr = bean(ItemDao::class)->findAllByUid($globalInfo->getUid());


        /**
         * 读取后端可调控配置
         */
        $config = Config::loadJson('clientConfig');
        //$sendLogBool = CenterUserSpecial::checkUsertByCustomerId($globalInfo->customerId);
        $sendLogBool = false;


        //标记登录时间
        $redis = bean('redis');


        //查询钻石数量（腾讯托管货币）
        $payService = bean(PayServiceInterface::class);
        $balance = $payService->getBalance();
        $userInfo->setGoldCoin($balance);

        bean(UserInfoDao::class)->updateUserInfo($userInfo);

        $userInfoArr =  bean(UserInfoDao::class)->findOneByUidQuery($globalInfo->getUid());
        /**
         * loginLog
         */
        $loginLog = new LoginLog();
        $loginLog->topLevel = $userInfoArr["new_level"] - 1;
        $itemsMap = ArrayHelper::map($allItemsArr, 'item', 'number');
        $loginLog->Silver = $userInfoArr['silver_coin'];
        $loginLog->Gold = $userInfoArr['gold_coin'];
        $loginLog->CountBoom = $itemsMap[ItemID::BOMB] ?? 0;
        $loginLog->CountColor = $itemsMap[ItemID::GUN] ?? 0;
        $loginLog->CountColumn = $itemsMap[ItemID::ROCKET_COLUMN] ?? 0;
        $loginLog->CountRow = $itemsMap[ItemID::ROCKET_ROW] ?? 0;
        $loginLog->CountRefresh = $itemsMap[ItemID::SWITCH] ?? 0;
        $loginLog->CountSingle = $itemsMap[ItemID::SINGLE] ?? 0;
        $loginLog->CountThreeStep = $itemsMap[ItemID::PLUS_3_STEP] ?? 0;
        $loginLog->Prosperity = $userInfoArr['prosperity'] ?? 0;
        $loginLog->send();
        $redis->hSET(RedisKey::USER_LAST_LOGIN, $globalInfo->getUid(), json_encode($loginLog->getAttributes()));


        /**
         * 连续签到的天数
         */
        $signService = bean(SignService::class);
        $signInfo = $signService->getSignInfo();

        /**
         * 孵化登陆时请求的数据
         */
        $hatchService = bean(HatchService::class);
        $hatchInfo = $hatchService->getHatchAllInfo();


        /**
         * 用户
         */
        return [
            'code' => 1,
            'userInfo' => $userInfoArr,
            'Items' => $allItemsArr,
            'isDirectlySend' => 0,
            'config' => $config,
            'sendLogBool' => $sendLogBool,
            'signInfo' => $signInfo,
            'serverTimeStamp' => time(),
            'activityShop' => Shop::getActiveShopEnable(),
            'hatchInfo' => $hatchInfo,
            'dotProbability' => DotProbabilityConst::PROBABILITY,
            'isPlatformVip' => $isPlatformVip,
        ];
    }

    /**
     * @RequestMapping(route="create")
     * 创建用户信息,初始化金钱和道具
     * @param int $goldCoin
     * @return array
     */
    public function actionCreate($goldCoin = 200)
    {
        $globalInfo = globalInfo();
        if (!empty(bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid()))) {
            return $this->returnError(Message::USER_INFO_EXISTED);
        }

        $isSuccess = bean(UserInfoDao::class)->createNewUser($globalInfo->getUid(), $goldCoin);

        if (!$isSuccess) {
            return $this->returnError(Message::CREATE_USER_FAIL);
        }

        //上传数据
        ReportDataService::ReportUserName($isSuccess->getUsername(), $globalInfo->getThirdId());
        /**
         * 赠送道具
         */
        $unlockConfig = Config::loadJson('unlockLevel');
        foreach ($unlockConfig as $c) {
            bean(PropertyService::class)->handleOne($c ['id'], $c ['reward'], FlowActionConst::ACTION_ITME_LEVEL_PASS_REWARD, 'user init unlock items');

        }

        $createUserLog = new CreateUserLog();
        $createUserLog->customerId = $globalInfo->getUid();
        $createUserLog->send();

        return $this->returnSuccess();
    }


    /**
     * @RequestMapping(route="getinfo")
     * @return array
     */
    public function actionGetInfo()
    {
        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUidAndSelect(
            ['lives',
                'gold_coin',
                'silver_coin',
                'username',
                'new_level',
                'remainStars',
                'avatar',
                'population',
                'prosperity'], $globalInfo->getUid());

        if (!$userInfo) {
            return $this->returnError(Message::USER_NOT_FOUND);
        }

        $payService = bean(PayServiceInterface::class);
        $balance = $payService->getBalance();
        $userInfo['gold_coin'] = $balance;

        foreach ($userInfo as &$info) {
            if (is_numeric($info)) {
                $info = (int)$info;
            }
        }

        $items = bean(ItemDao::class)->findItemsByUid($globalInfo->getUid());
        foreach ($items as &$item) {
            $item['number'] = (int)$item['number'];
            $item['item'] = (int)$item['item'];
        }
        $userInfo['items'] = $items ? $items : [];

        return $this->returnData($userInfo);
    }

    /**
     * @RequestMapping(route="getallfriends")
     * 获取所有好友的信息
     * @return array
     */
    public function actionGetAllFriends()
    {
        $globalInfo = globalInfo();
        $friends = MyFriendsDao::findAllFriendsInfo($globalInfo->getUid());
        ArrayHelper::multisort($friends, 'last_login_at', SORT_DESC);

        $friendLives = bean(FriendLivesService::class)->initMine();
        $remain_request_num = $friendLives->getRequestRemainNum();
        $remain_send_num = $friendLives->getSendRemainNum();

        return [
            'code' => 1,
            'friends' => $friends,
            'remainNum' => [
                'request' => $remain_request_num,
                'send' => $remain_send_num
            ],
            'friendLivesList' => [
                'request' => $friendLives->getRequestList(),
                'send' => $friendLives->getSendList()
            ]
        ];
    }

    /**
     * @RequestMapping(route="update")
     * 更新用户信息
     * @param array $userInfo ,example ["username"=>$username]
     */
    public function actionUpdate($userInfo)
    {
        $globalInfo = globalInfo();
        $allowUpdateField = [
            "username",
            "avatar",
            "tencentVip"
        ];
        foreach ($userInfo as $fieldName => $value) {
            if (!in_array($fieldName, $allowUpdateField, true)) {
                return $this->returnError(Message::USER_UPDATE_FORBIDDEN_FIELD);
            }

            //检测敏感词汇
            if ($fieldName == 'username') {
                $client = new Client(['base_uri' => 'http://' . config('tss_url.sensitive'), 'adapter' => 'co']);
                try {
                    $response = $client->request('POST', 'uic', ['body' => json_encode([
                            'openid' => $globalInfo->getThirdId(), 'msg' => $value,]),
                            'headers' => ['Content-Type' => 'application/json; charset=UTF-8']]
                    )->getResponse();

                } catch (\Exception $e) {
                    App::error('sensitive server error,error info was:' . $e->getMessage());
                }
                $res = json_decode($response->getBody()->getContents(), true);
                $userInfo['username'] = isset($res['msg']) ? $res['msg'] : $userInfo['username'];

                //上报数据
                ReportDataService::ReportUserName($userInfo['username'], $globalInfo->getThirdId());
            }
        }

        $result = bean(UserInfoDao::class)->updateAll($globalInfo->getUid(), $userInfo);
        if ($result === false) {
            return $this->returnError(Message::USER_UPDATE_UNKNOWN_ERROR);
        }

        return $this->returnSuccess();
    }

    /**
     * @RequestMapping(route="getinfobyopenid")
     * 通过openId获取用户uuid
     * @param String $third 第三方
     */
    public function actionGetInfoByOpenId($openIds)
    {
        if (empty($openIds) || !is_array($openIds)) {
            return [
                "code" => Message::USER_OPENID_CANNOT_EMPTY
            ];
        }
        $result = [];
        foreach ($openIds as $openId) {
            $checkId = bean(CenterUserDao::class)->findOneByThirdIdFromCache($openId);
            if (empty($checkId)) {
                $result [$openId] = 'unKnown';
            } else {
                $result [$openId] = $checkId->getUid();
            }
        }
        return [
            'code' => 1,
            'result' => $result
        ];
    }

    /**
     * 实时在线心跳包
     * @RequestMapping(route="online")
     * @param int $topLevel
     */
    public function actionOnline($topLevel)
    {
        return $this->returnSuccess();
        
        $globalInfo = globalInfo();
        $redis = bean('redis');

        $onlineCntKey = RedisKey::ONLINE_CNT;
        $redis->zAdd($onlineCntKey, time(), $globalInfo->getUid());
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        if ($userInfo->getStatus() != UserInfoDao::STATUS_ONLINE) {
            $resMsg = PunishService::OnlinePunishInfo($userInfo, $globalInfo->getThirdId());//userInfo openId
            if ($resMsg) {
                return $this->returnError(Message::OFF_LINE, $resMsg);
            }
        }

        //上报数据
        ReportDataService::ReportOnline($globalInfo->getThirdId(), $globalInfo->getUid());

        return $this->returnSuccess();
    }

    /**
     * @RequestMapping(route="entidata")
     * @param $str
     * @return array
     */
    public function actionEntiData($str)
    {

        $redis = bean('redis');
        $iSequence = $redis->incr('enti_data_isequence');

        $logAnti = new LogAnti();
        $logAnti->iSequence = $iSequence;
        $logAnti->strData = $str;
        $logAnti->send();


        return $this->returnSuccess();
    }

    /**
     * @RequestMapping(route="getservicetime")
     * @return array
     */
    public function actionGetServiceTime()
    {
        return [
            'code' => 1,
            'time' => time()
        ];
    }

    /**
     * @RequestMapping(route="getinfobyuid")
     * @param $uidArr
     * @return array
     */
    public function actionGetInfoByUid($uidArr)
    {
        $globalInfo = globalInfo();

        //返回好友信息列表，redis缓存10分钟
        $userInfoArr = bean(UserInfoDao::class)->getAllByUidArr($uidArr);
        $friendsUidArr = MyFriendsDao::findAllFriendsUid($globalInfo->getUid());

        //查询的uid是不是globalInfo->uid的好友
        foreach ($userInfoArr as $key => $userInfo) {
            if (in_array($userInfo['uid'], $friendsUidArr)) {
                $userInfoArr[$key]['isFriend'] = 1;//是好友
            } else {
                $userInfoArr[$key]['isFriend'] = 0;//不是好友
            }
        }
        return $this->returnData(['userInfoArr' => $userInfoArr]);
    }

    /**
     * @RequestMapping(route="friendsrankinglist")
     * @param $uidArr
     * @return array
     */
    public function actionFriendsRankingList($uidArr)
    {
        $globalInfo = globalInfo();

        if ($punishObj = PunishDetail::findOne(['openId' => $globalInfo->getThirdId(), 'type' => PunishType::BAN_RANKING_PROSPERITY])) {//判断是否被禁止排行榜
            $end_at = $punishObj->end_at;
            $reason = $punishObj->reason;
            $dateTime = date("Y-m-d H:i:s", $end_at);
            return $this->returnError(Message::BAN_RANKING_STATUS, $reason . "|" . $dateTime);

        }
        //返回好友信息列表，redis缓存10分钟
        $userInfoArr = bean(UserInfoDao::class)->getAllByUidArr($uidArr);
        $friendsUidArr = MyFriendsDao::findAllFriendsUid($globalInfo->getUid());
        $redis = bean('redis');
        $banProsperityUserArr = $redis->hGetAll(RedisKey::BAN_PROSPERITY);//里面存的是uid=>openId
        foreach ($userInfoArr as $key => $user) {

            if (in_array($user['uid'], $friendsUidArr)) {
                $userInfoArr[$key]['isFriend'] = 1;//是好友
            } else {
                $userInfoArr[$key]['isFriend'] = 0;//不是好友
            }

            if (isset($banProsperityUserArr[$user['uid']])) {
                $userInfoArr[$key]['isBanProsperity'] = 1;
            } else {
                $userInfoArr[$key]['isBanProsperity'] = 0;
            }
        }
        //其中有些人被禁止繁荣度已经结束了，自动解封
        foreach ($userInfoArr as $key => $user) {
            if ($userInfoArr[$key]['isBanProsperity'] == 1) {
                $openId = $banProsperityUserArr[$user['uid']];
                if ($punishObj = PunishDetail::findOne(['openId' => $openId, 'type' => PunishType::BAN_RANKING_PROSPERITY])) {//判断是否被禁止排行榜
                    unset($userInfoArr[$key]);
                } else {//已经自动解封了
                    $redis->hDel(RedisKey::BAN_PROSPERITY, $user['uid']);
                }
            }
        }


        return $this->returnData(['userInfoArr' => $userInfoArr]);
    }

}
