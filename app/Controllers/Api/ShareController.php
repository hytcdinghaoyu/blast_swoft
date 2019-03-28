<?php

namespace App\Controllers\Api;

use App\Constants\Message;
use App\Constants\FlowActionConst;
use app\constants\ShareRewardType;
use App\Controllers\CommonController;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Constants\RedisKey;
use Swoft\Http\Server\Bean\Annotation\Controller;

/**
 * 用户模块.
 * @Controller(prefix="/share")
 */
class ShareController extends CommonController
{
    /**
     * @RequestMapping(route="reward")
     */
    public function actionReward($shareType)
    {
        $globalInfo= globalInfo();
        if (!ShareRewardType::isValidValue($shareType)) {
            return $this->returnError(Message::INVALID_SHARE_REWARD_TYPE);
        }
        $redis = bean('redis');
        $rewardConfig = Config::loadJson('friendShareReward');
        $rewardArr = $rewardConfig[$shareType]['rewards'];
        $rewardLimit = $rewardConfig[$shareType]['limit'];

        $shareRecordKey = $this->getShareRecordKey($shareType,date("Ymd"),$globalInfo->getUid());
        $curRewardNum = $redis->get($shareRecordKey);

        if ($curRewardNum>=$rewardLimit){
            return $this->returnData(['rewardArr' => []]);//超过次数无奖励
        }


        $redis->set($shareRecordKey,$curRewardNum+1,3600*24);
        bean(PropertyService::class)->handleBatch($rewardArr, FlowActionConst::ACTION_REWARD_SHARE,"friend_share_reward");

        return $this->returnData(['rewardArr' => $rewardArr]);

    }

    /**
     * @RequestMapping(route="getsharenum")
     * @return array
     */
    public function actionGetShareNum()
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');
        $rewardConfig = Config::loadJson('friendShareReward');
        $shareTypeArr = array_keys($rewardConfig);
        $shareNumArr = [];
        foreach ($shareTypeArr as $shareType) {
            $shareRecordKey = $this->getShareRecordKey($shareType,date("Ymd"),$globalInfo->getUid());
            $num = $redis->get($shareRecordKey);
            $shareNumArr[$shareType] = (int)$num;
        }

        return $this->returnData(['shareNumArr' => $shareNumArr]);

    }

    /**
     * 获取所有奖励列表
     * @return array|mixed
     */
    public function getRewardList()
    {
        return Config::loadJson('friendShareReward');

    }
    public function getShareRecordKey($shareType, $day, $uid)
    {
        return sprintf(RedisKey::SHARE_REWARD_RECORD, $shareType, $day, $uid);
    }

}

