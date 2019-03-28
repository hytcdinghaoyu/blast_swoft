<?php

namespace App\Controllers\Api;

use App\Constants\MoneyType;
use App\Models\MailMsg;
use App\Constants\Message;
use App\Constants\MailMsgType;
use App\Constants\RedisKey;
use App\Controllers\CommonController;
use App\Models\Dao\MyFriendsDao;
use App\Models\Dao\UserInfoDao;
use App\Services\FriendLivesService;
use App\Models\Entity\myfriends;
use yii\helpers\ArrayHelper;
use Swoft\Bean\Annotation\Inject;
use App\Services\MailerService;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;

/**
 * 用户模块.
 * @Controller(prefix="friends")
 */

class FriendsController extends CommonController
{

    /**
     * @Inject
     * @var MailerService
     */
    private $mailer;
    const RECOMMEND_TIME_LIMIT = 10800;//  推荐好友3小时内登陆

    const DAILY_RECOMMEND_LIMIT = 10;//  每日最多加10个好友

    const SUM_FRIEND_LIMIT = 50;//  一共最多加50个好友

    /**
     *
     *@RequestMapping(route="getinfo")
     */

    public function actionGetInfo()
    {
        $friendLives = bean(FriendLivesService::class)->initMine();

        return [
            'code' => 1,
            'remainNum' => [
                'request' => $friendLives->getRequestRemainNum(),
                'send' => $friendLives->getSendRemainNum()
            ],
            'friendLivesList' => [
                'request' => $friendLives->getRequestList(),
                'send' => $friendLives->getSendList()
            ]
        ];
    }

    /**
     * 向好友请求赠送生命
     * @RequestMapping(route="requestlives")
     * @return array
     */
    public function actionRequestLives(array $uid_arr)
    {

        $globalInfo= globalInfo();
        $uid = $globalInfo->getUid();
        $uid_arr = array_diff($uid_arr, [$uid]);

        $friendLives = bean(FriendLivesService::class)->initMine();
        $msg = new MailMsg();
        $msg->setSenderUid($uid);
        $msg->setType(MailMsgType::REQUEST_LIVES);

        list($current_request, $total_request) = $friendLives->incrRequestNum($uid_arr);

        foreach ($current_request as $fuid) {
            $msg->setReceiverUid($fuid);
            $this->mailer->send($msg);
        }

        return [
            'code' => 1,
            'remain_request_num' => $friendLives->getRequestRemainNum(),
            'requested_list' => $total_request
        ];
    }

    /**
     *
     * 直接赠送生命，每天第一次登陆游戏时弹窗赠送生命
     * @RequestMapping(route="sendlivesdirectly")
     * @return array
     */
    public function actionSendLivesDirectly(array $uid_arr)
    {
        $globalInfo =globalInfo();
        $uid = $globalInfo->getUid();
        $uid_arr = array_diff($uid_arr, [$uid]);
        $friendLives = bean(FriendLivesService::class)->initMine();
        $msg = new MailMsg();
        $msg->setSenderUid($uid);
        $msg->setType(MailMsgType::SEND_LIVES)->setReward([MoneyType::LIVE=>1]);

        list($current_send, $total_send) = $friendLives->incrSendNum($uid_arr);

        foreach ($current_send as $fuid) {
            $msg->setReceiverUid($fuid);
            $this->mailer->send($msg);
        }
        return [
            'code' => 1,
            'remain_send_num' => $friendLives->getSendRemainNum(),
            'sended_list' => $total_send
        ];
    }

    /**
     * 请求添加好友
     * @param $fuidArr
     * @RequestMapping(route="requestaddfriend")
     * @return array
     */
    public function actionRequestAddFriend($fuidArr)
    {

        $globalInfo =globalInfo();
        $uid = $globalInfo->getUid();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        $contentArr = ['senderUid' => $uid, 'senderAvatar' => $userInfo->getAvatar(), 'senderName' => $userInfo->getUsername()];
        $msg = new MailMsg();
        $msg->setSenderUid($uid);
        $msg->setType(MailMsgType::REQUEST_ADD_FRIENDS)->setContent(json_encode($contentArr));
        
        foreach ($fuidArr as $fuid) {
            $msg->setReceiverUid($fuid);
            $this->mailer->send($msg);
        }

        return $this->returnSuccess();
    }

    /**
     * 确认添加好友
     * @RequestMapping(route="ensureaddfriend")
     * @return array
     */
    public function actionEnsureAddFriend($fuid)
    {
        $globalInfo = globalInfo();
        $friendsUidArr = bean(MyFriendsDao::class)->findAllFriendsUid($globalInfo->getUid());
        $redis = bean('redis');
        $redisKey = sprintf(RedisKey::DAILY_RECOMMEND_NUM, $globalInfo->getUid(), date("Ymd"));

        //每天添加的好友数
        $dailyNum = $redis->get($redisKey);
        $dailyNum += 1;

        //游戏一共添加的好友数
        $sumNum = count($friendsUidArr) + 1;

        if ($dailyNum > static::DAILY_RECOMMEND_LIMIT) {
            return $this->returnError(Message::RECOMMEND_FRIEND_SEND_NUM_LIMITED);
        }

        if ($sumNum > static::SUM_FRIEND_LIMIT) {
            return $this->returnError(Message::SUM_FRIEND_SEND_NUM_LIMITED);
        }

        //添加过的好友再添加就报错

        if (in_array($fuid, $friendsUidArr)) {
            return $this->returnError(Message::RECOMMEND_FRIEND_UID_INVALID);
        }

        //互加好友
        $myFriends = new MyFriends();
        $myFriends->setUid($globalInfo->getUid());
        $myFriends->setFuid($fuid);
        $myFriends->setCreatedAt(time());
        $myFriends->setUpdatedAt(time());
        $myFriends->save()->getResult();

        $myFriends = new MyFriends();
        $myFriends->setUid($fuid);
        $myFriends->setFuid($globalInfo->getUid());
        $myFriends->setCreatedAt(time());
        $myFriends->setUpdatedAt(time());
        $myFriends->save()->getResult();


        //设置redis
        $redis->SETEX($redisKey, 86400, $dailyNum);

        $restRecommendNum = static::DAILY_RECOMMEND_LIMIT - $dailyNum;
        return $this->returnData([
            'restRecommendNum' => $restRecommendNum
        ]);


    }


    /**
     *
     * @RequestMapping(route="getrecommendfriends")
     * @return mixed
     */

    public function actionGetRecommendFriends()
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');

        $friendsInfo = $this->getRecommendList($globalInfo);

        $redisKey = sprintf(RedisKey::DAILY_RECOMMEND_NUM, $globalInfo->getUid(), date("Ymd"));
        $restRecommendNum = static::DAILY_RECOMMEND_LIMIT - $redis->get($redisKey);
        return $this->returnData([
            'friendsInfo' => $friendsInfo,
            'restRecommendNum' => $restRecommendNum
        ]);

    }


    public function getRecommendList($globalInfo)
    {
        $redis = bean('redis');
        $globalInfo = globalInfo();
        $redisArr = $redis->ZRANGEBYSCORE(RedisKey::ONLINE_CNT, time() - static::RECOMMEND_TIME_LIMIT, time() + 60);
        $allUserArr = array_slice($redisArr, 0, 100);
        $tmpArr = bean(MyFriendsDao::class)->findAllFriendsInfo($globalInfo->getUid());
        $addedUidArr = ArrayHelper::getColumn($tmpArr, 'uid');
        $addedUidArr[] = $globalInfo->getUid();//去掉自己

        $count = 0;
        $notAddedFriendsArr = [];
        //筛选不能是自己已经添加的好友
        foreach ($allUserArr as $uid) {
            if (!in_array($uid, $addedUidArr)) {
                $notAddedFriendsArr[] = $uid;
                $count++;
                if ($count == 50)
                    break;
            }
        }
        //可能取出来的小于10个
        $randLimit = count($notAddedFriendsArr) >= static::DAILY_RECOMMEND_LIMIT ? static::DAILY_RECOMMEND_LIMIT : count($notAddedFriendsArr);

        //实时在线可能为空
        if (!empty($notAddedFriendsArr)) {
            $randIndexArr = array_rand($notAddedFriendsArr, $randLimit);//取随机数组
        } else {
            $randIndexArr = [];
        }

        $randUidArr = [];
        if ($randIndexArr) {
            foreach ($randIndexArr as $index) {
                $randUidArr[] = $notAddedFriendsArr[$index];
            }
        }
        $finalArr = bean(UserInfoDao::class)->getAllByUidArr($randUidArr);
        return $finalArr;
    }


}
