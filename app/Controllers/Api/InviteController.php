<?php

namespace App\Controllers\Api;


use App\Constants\FlowAction;
use App\Constants\Message;
use App\Constants\RedisKey;
use App\Controllers\CommonController;
use App\Services\PropertyService;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Constants\InviteRewardType;
use App\Constants\FlowActionConst;
use App\Utils\Config;

/**
 * 用户模块.
 * @Controller(prefix="/invite")
 */
class InviteController extends CommonController
{
    /**
     * @RequestMapping(route="init")
     */
    public function actionInit()
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');

        $sendNumKey = $this->getSendNumKey(date("Ymd"), $globalInfo->getUid());
        $todayInviteNum = $redis->get($sendNumKey);//今天的邀请次数
        $todayInviteNum = empty($todayInviteNum) ? 0 : $todayInviteNum;

        $successNumKey = $this->getSuccessNumKey($globalInfo->getUid());
        $inviteSuccessNum = $redis->get($successNumKey);//邀请成功的人数
        $inviteSuccessNum = empty($inviteSuccessNum) ? 0 : $inviteSuccessNum;

        $rewardRecordKey = $this->getRewardRecordKey($globalInfo->getUid());
        $rewardRecordArr = $redis->hGetAll($rewardRecordKey);//领取记录
        $rewardRecordArr = is_array($rewardRecordArr) ? $rewardRecordArr : [];


        return $this->returnData([
            'todayInviteNum' => (int)$todayInviteNum,
            'inviteSuccessNum' => (int)$inviteSuccessNum,
            'rewardRecordArr' => $rewardRecordArr
        ]);
    }

    /**
     * @RequestMapping(route="progressreward")
     * @return array
     */
    public function actionProgressReward(int $progress)
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');
        $rewardTmp = $this->getRewardList();
        $rewardList = $rewardTmp[InviteRewardType::PROGRESS_REWARD];
        $successNumKey = $this->getSuccessNumKey($globalInfo->getUid());
        $inviteSuccessNum = $redis->get($successNumKey);//邀请成功的人数
        $inviteSuccessNum = empty($inviteSuccessNum) ? 0 : $inviteSuccessNum;
        if (!in_array($progress, array_keys($rewardList)) || $progress > $inviteSuccessNum) {//进度必须为3，6，9，12，15，且领奖传的进度必须<=redis里存的
            return $this->returnError(Message::FRIEND_REWARD_PROGRESS_INVALID);
        }
        $globalInfo = globalInfo();

        $redisKey = $this->getRewardRecordKey($globalInfo->getUid());

        if ($this->rewardGotten($globalInfo->getUid(), $progress)) {//已经领取过了
            return $this->returnError(Message::FRIEND_REWARD_GOTTEN);
        }

        $redis->hSet($redisKey, $progress, 1);
        $rewardArr = $rewardList[$progress];
        bean(PropertyService::class)->handleBatch($rewardArr, FlowActionConst::ACTION_REWARD_INVITE_PROGRESS, "friend_invite_progress_reward");

        return $this->returnData(['rewardArr' => $rewardArr]);


    }

    /**
     * 客户端发送邀请成功
     * @return array
     * @RequestMapping(route="sendinvite")
     */
    public function actionSendInvite()
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');
        $sendNumKey = $this->getSendNumKey(date("Ymd"), $globalInfo->getUid());

        $successNumKey = $this->getSuccessNumKey($globalInfo->getUid());
        $rewardList = $this->getRewardList();
        $rewardArr = [];

        $todayOldNum = $redis->get($sendNumKey);
        $todayOldNum = empty($todayOldNum) ? 0 : $todayOldNum;
        //发送数加1
        $redis->set($sendNumKey, $todayOldNum + 1, 3600 * 24);
        $todayNewNum = $todayOldNum + 1;

        if ($todayOldNum < InviteRewardType::DAILY_LIMIT) {//每天邀请次数小于3才增加邀请人数进度,每天只能领三次奖励
            $redis->incr($successNumKey);
            $rewardArr = $rewardList[InviteRewardType::EACH_INVITE];
            bean(PropertyService::class)->handleBatch($rewardArr, FlowActionConst::ACTION_REWARD_SEND_INVITE, "friend_send_invite_reward");
        }
        $inviteSuccessNum = $redis->get($successNumKey);

        return $this->returnData(['todayInviteNum' => (int)$todayNewNum, 'inviteSuccessNum' => (int)$inviteSuccessNum, 'rewardArr' => $rewardArr]);


    }

    /**
     * 检查奖励是否被领取
     */
    public function rewardGotten($uid, $progress)
    {
        $redis = bean('redis');
        $redisKey = $this->getRewardRecordKey($uid);
        $res = $redis->hGet($redisKey, $progress);
        if ($res == 1) {
            return true;
        }
        return false;


    }

    /**
     * 获取所有奖励列表
     * @return array|mixed
     */
    public function getRewardList()
    {
        return Config::loadJson('friendInviteReward');

    }

    /**
     * 每日发送邀请的次数
     * @param $date
     * @param $uid
     * @return string
     */
    public function getSendNumKey($date, $uid)
    {
        return sprintf(RedisKey::SEND_INVITE_DAILY_NUM, $date, $uid);
    }

    /**
     * 邀请成功的次数
     * @param $uid
     * @return string
     */
    public function getSuccessNumKey($uid)
    {
        return sprintf(RedisKey::INVITE_SUCCESS_NUM, $uid);
    }

    /**
     * 邀请成功奖励领取记录
     * @param $date
     * @param $uid
     * @return string
     */
    public function getRewardRecordKey($uid)
    {
        return sprintf(RedisKey::INVITE_REWARD_RECORD, $uid);
    }

}

