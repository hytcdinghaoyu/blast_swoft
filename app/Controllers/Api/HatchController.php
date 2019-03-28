<?php

namespace App\Controllers\Api;


use App\Constants\FlowAction;
use App\Constants\Message;
use App\Constants\RedisKey;
use App\Controllers\CommonController;
use App\Services\HatchService;
use App\Services\PropertyService;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Constants\InviteRewardType;
use App\Constants\FlowActionConst;
use App\Utils\Config;


/**
 * 用户模块.
 * @Controller(prefix="/hatch")
 */
class HatchController extends CommonController
{

    /**
     * @RequestMapping(route="init")
     * @return array
     */
    public function actionInit()
    {
        /**
         * 孵化登陆时请求的数据
         */
        $hatchService = bean(HatchService::class);
        $hatchInfo = $hatchService->getHatchAllInfo();
        return $this->returnData(['hatchInfo' => $hatchInfo]);


    }
    /**
     * 客户端判断在活动内每过一关发一次请求
     * @RequestMapping(route="getreward")
     * @return array
     */
    public function actionGetReward()
    {
        $hatchService = bean(HatchService::class);
        if (!$hatchService->checkHatchTime()){//这里无法判断关卡，客户端关卡加1请求与获取奖励请求是异步的
            return $this->returnError(Message::HATCH_REWARD_FAIL);
        }

        //先更新进度，进度+1
        $hatchService->updateUserProgress();
        //获取更新后的进度
        $curProgress = $hatchService->getCurProgress();
        $rewardList = $hatchService->getRewardList();
        $res = $hatchService->getReward($curProgress);
        if (!$res) {
            return $this->returnError(Message::HATCH_REWARD_FAIL);
        }
        //不在配置的奖励进度里面返回空数组
        $rewardArr = empty($rewardList[$curProgress]) ? [] : $rewardList[$curProgress];
        bean(PropertyService::class)->handleBatch($rewardArr, FlowActionConst::ACTION_SEND_HATCH_REWARD,'hatch_reward');
        return $this->returnData(['rewardList'=>$rewardArr]);

    }

}

