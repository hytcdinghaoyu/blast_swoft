<?php

namespace App\Controllers\Api;

use App\Constants\Message;
use App\Constants\FlowActionConst;
use App\Constants\TaskProgressID;
use App\Constants\TaskProgressType;
use App\Controllers\CommonController;
use App\Services\MainProgressService;
use App\Services\TaskProgressService;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Services\PropertyService;
use Swoft\Http\Server\Bean\Annotation\Controller;

/**

 * @Controller(prefix="/taskprogress")
 */
class TaskProgressController extends CommonController
{
    /**
     * @RequestMapping(route="init")
     * 获取总进度
     */
    public function actionInit()
    {
        $taskProgressService =  new MainProgressService();
        $progress            = $taskProgressService->getProgress();
        $extra               = $taskProgressService->getExtra();

        $ret = [];
        foreach ($progress as $targetId => $num) {
            $ret[$targetId] = [
                'num'   => $num,
                'extra' => isset($extra[$targetId]) ? $extra[$targetId] : [],
            ];
        }

        return $this->returnData([
            'progress' => $ret,
        ]);
    }

    /**
     *@RequestMapping(route="update")
     * 更新进度
     *
     * @param int   $targetId
     * @param int   $incNum
     * @param array $extra
     *
     * @return array
     */
    public function actionUpdate(int $targetId, int $incNum, array $extra = [], bool $isReset = false)
    {
        if ( ! TaskProgressID::isValidValue($targetId)) {
            return $this->returnError(Message::TARGET_ID_INVALID);
        }

        $targetTypeArr = [TaskProgressType::MAIN,TaskProgressType::DAILY, TaskProgressType::WEEKLY];
        foreach ($targetTypeArr as $type) {
            $taskProgressService = TaskProgressService::factory($type);
            $taskProgressService->updateProgress($targetId, $incNum, $isReset);

            if ($extra) {
                $taskProgressService->updateExtra($targetId,
                    json_encode($extra));
            }

        }

        return $this->returnSuccess();
    }


    /**
     *@RequestMapping(route="getall")
     * @return array
     */
    public function actionGetAll(int $type = 0)
    {
        if ( ! TaskProgressType::isValidValue($type)) {
            return $this->returnError(Message::TASK_TYPE_INVALID);
        }

        $taskProgressService = TaskProgressService::factory($type);
        $records = $taskProgressService->getRewardConfig();

        return $this->returnData(
            ['records' => $records]
        );
    }

    /**
     *@RequestMapping(route="reward")
     * @return array
     */
    public function actionReward(int $type, int $taskId)
    {
        $taskProgressService = TaskProgressService::factory($type);

        $rewardConfig = $taskProgressService->getRewardConfigFromCache();
        if(!isset($rewardConfig[$taskId])){
            //taskId不存在
            return $this->returnError(Message::TASK_ID_NOT_FOUND);
        }

        //已经领取过
        if(isset($rewardConfig[$taskId]['is_rewarded']) && $rewardConfig[$taskId]['is_rewarded']){
            return $this->returnError(Message::TASK_ID_REWARDED);
        }

        //任务进度不达标
        $targetNum = $rewardConfig[$taskId]['target_num'];
        $currentNum = $taskProgressService->getProgressByTargetId($rewardConfig[$taskId]['target_type']);
        if($currentNum < $targetNum){
            return $this->returnError(Message::TARGET_NOT_ENOUGH);
        }

        //发奖
        bean(PropertyService::class)->handleBatch($rewardConfig[$taskId]['reward'], FlowActionConst::TASK_PROGRESS, date('Ymd'));
        $rewardConfig[$taskId]['is_rewarded'] = true;
        $taskProgressService->cacheRewardConfig($rewardConfig);


        return $this->returnData(
            ['reward' => $rewardConfig[$taskId]['reward']]
        );
    }
}