<?php

namespace App\Controllers\Api;

use App\Constants\BlockID;
use App\Constants\Message;
use App\Constants\FlowActionConst;
use App\Constants\MoneyType;
use App\Controllers\CommonController;
use App\Models\Dao\TaskDao;
use App\Models\Dao\UserInfoDao;
use App\Models\Eliminated;
use App\Models\EliminatedDaily;
use App\Models\Entity\Task;
use App\Services\SignService;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Db\Query;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Constants\RedisKey;
use Swoft\Http\Server\Bean\Annotation\Controller;
use yii\helpers\ArrayHelper;

/**
 * 用户模块.
 * @Controller(prefix="/task")
 */
class TaskController extends CommonController
{
    const TASK_TYPE_PASS_LEVEL = '1008';
    const TASK_TYPE_GET_STARS = '2';
    const TASK_TYPE_FB_SHARE = '3';

    public $dailyItems = [BlockID::PIG_NORMAL];

    /**
     * @RequestMapping(route="questreward")
     * 获取任务奖励
     *
     * @param $isMaster 1.主线任务;0.日常任务;
     * @param int|array $taskId 1.任务ID;0,[$type=>$id]
     * @return array
     */
    public function actionQuestReward($isMaster, $taskId)
    {
        $taskConfig = Config::loadJson('task');
        $taskType = $isMaster ? "task" : "dailyQuest";
        $idExist = true;
        $taskDetail = [];
        $questId = '';
        //检查id是否正确,如果正确,取出相应的配置
        if ($isMaster) { //主线任务
            $questId = $taskId;
            $idExist = isset ($taskConfig[$taskType][$questId]);
            if ($idExist) {
                $taskDetail = $taskConfig[$taskType][$questId];
            }
        } else {//日常任务
            $questType = key($taskId);
            $questId = current($taskId);
            $idExist = isset ($taskConfig[$taskType][$questType][$questId]);
            if ($idExist) {
                $taskDetail = $taskConfig[$taskType][$questType][$questId];
            }
        }
        //如果不正确返回错误信息
        if (!$idExist) {
            return [
                'code' => 31301,
                'message' => 'task id not in config file!',
                'task_id' => $questId
            ];
        }

        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        $uid = $globalInfo->getUid();
        $redis = bean('redis');
        if ($isMaster) {//如果是主线任务
            // 取出任务类型
            $type = $taskDetail [2];
            // 取出任务完成目标
            $targ = $taskDetail [0];
            // 如果是通过多少关卡类型
            if ($type == self::TASK_TYPE_PASS_LEVEL) {
                //检查当前通过的关数
                $completed = ['2' => $userInfo->getNewLevel() - 1];
            } else {//如果是用新元素兑换物品
                $completed = $this->getHatchlingByLevel($uid, $userInfo->getNewLevel() - 1, array_keys($targ));

            }

            //判断是否满足目标;
            $judge = self::destinationCmp($targ, $completed);
            //如果不满足
            if (!$judge) {
                //返回错误信息
                return [
                    'code' => 31302,
                    'message' => 'task target not complete!'
                ];
            }
            //判断是否已经领过奖励(包括主线任与日常任务) 主线任务在数据库中
            $keyMasterFinished = bean(TaskDao::class)->getKeyByType("masterFinished", $uid);
            $masterFinishedString = $redis->get($keyMasterFinished);

            if ($redis !== false && !empty($masterFinishedString)) {
                $masterFinishedArr = json_decode($masterFinishedString, true);
                if (in_array($questId, $masterFinishedArr)) {
                    return [
                        'code' => 31303,
                        'message' => 'task has been rewarded!',
                        'task_id' => $questId
                    ];
                }
            } else {
                $check = bean(TaskDao::class)->findOne($uid, $taskId);
                if (!empty($check)) {
                    return [
                        'code' => 31303,
                        'message' => 'task has been rewarded!',
                        'task_id' => $questId
                    ];
                }
            }
            //更新task表，新增一条记录
            bean(TaskDao::class)->createOne($uid, $taskId);
            //判断redis中是否存在记录，如果存在更新该值
            bean(TaskDao::class)::findAllMaster($uid, true);
        } else {//如果是日常任务
            /**
             * redis不可用时抛出异常，客户端稍后重发
             */
            if ($redis === false) {
                throw new \RedisException();
            }
            //判断该任务是不是当天的日常任务
            $keyDaily = bean(TaskDao::class)->getKeyByType("dailyTask", $uid);
            $dailyTaskString = $redis->get($keyDaily);
            $dailyTaskArr = json_decode($dailyTaskString, true);
            if (empty($dailyTaskString)) {
                // 返回错误信息
                return [
                    'code' => 31304,
                    'message' => 'task is not exist today!'
                ];
            }
            if (!isset($dailyTaskArr[$questType]) || !in_array($questId, $dailyTaskArr[$questType])) {
                // 返回错误信息
                return [
                    'code' => 31304,
                    'message' => 'task is not exist today!'
                ];
            }
            // 取出任务完成目标
            $targ = $taskDetail [0];
            $keyEliminated = Eliminated::getKeyByType("eliminated", $uid);
            $completedString = $redis->get($keyEliminated);
            $completedArr = json_decode($completedString, true);
            if (empty($completedString) || !self::destinationCmp($targ, $completedArr)) {
                //返回错误信息
                return [
                    'code' => 31302,
                    'message' => 'task target not complete!'
                ];
            }
            //判断是否已经领过奖励 日常任务在redis中
            $keyDailyFinished = bean(TaskDao::class)->getKeyByType("dailyFinished", $uid);
            $dailyFinishedString = $redis->get($keyDailyFinished);
            if (!empty($dailyFinishedString)) {
                $dailyFinishedArr = json_decode($dailyFinishedString, true);
                if (isset($dailyFinishedArr[$questType]) && in_array($questId, $dailyFinishedArr[$questType])) {
                    return [
                        'code' => 31303,
                        'message' => 'task has been rewarded!',
                        'task_id' => $questId
                    ];
                }
                $dailyFinishedArr[$questType][] = $questId;
            } else {
                $dailyFinishedArr[$questType][] = $questId;
            }
            //更新redis中的数据
            $redis->setEx($keyDailyFinished, 86400, json_encode($dailyFinishedArr));
        }

        //发放相应的奖励
        $rewards = $taskDetail[1];
        foreach ($rewards as $key => $value) {
            if (in_array($key, [1279, 1280])) {
                bean(PropertyService::class)->handleOne($key, $value, FlowActionConst::DETAIL_TASK_REWARD, $questId);
            } else {
                bean(PropertyService::class)->handleOne($key, $value, FlowActionConst::DETAIL_TASK_REWARD, $questId);

            }
        }
        //返回相应的数据列表

        return [
            'code' => 1,
            'taskId' => $taskId,
            'rewardDetail' => $rewards
        ];
    }

    /**
     * 获取主线任务和消除元素列表
     * @RequestMapping(route="masterlist")
     * @return array
     */
    public function actionMasterList()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $masterList = TaskDao::findAllMaster($uid);
        return [
            'code' => 1,
            'masterList' => $masterList,
        ];
    }

    /**
     * 获取日常任务和消除元素列表
     * @RequestMapping(route="questlist")
     * @return array
     */
    public function actionQuestList($dailyNum)
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $redis = bean('redis');
        /**
         * redis不可用时抛出异常，客户端稍后重发
         */
        if ($redis === false) {
            throw new \RedisException();
        }
        $keyDaily = bean(TaskDao::class)->getKeyByType("dailyTask", $uid);
        // 判断dailyNum是否大于0
        $dailyToday = [];
        if ($dailyNum > 0) {
            if ($dailyNum > 5) {
                $dailyNum = 5;
            }
            $dailyTaskString = $redis->get($keyDaily);
            $dailyTemp = json_decode($dailyTaskString, true);
            // 如果存在
            if (!empty($dailyTaskString)) {
                $dailyCount = 0;
                //获取活动个数
                foreach ($dailyTemp as $daily) {
                    $dailyCount += count($daily);
                }
                if ($dailyCount >= $dailyNum) {
                    // 根据dailynum直接取出
                    $dailyToday = $dailyTemp;
                } else {
                    $diffNum = $dailyNum - $dailyCount;
                    $dailyToday = self::getRandomDailyByNum($diffNum, $dailyTemp);
                    $redis->setEx($keyDaily, 86400, json_encode($dailyToday));
                }
            } else { // 如果不存在
                $dailyToday = self::getRandomDailyByNum($dailyNum);
                // 将数据存入redis
                $redis->setEx($keyDaily, 86400, json_encode($dailyToday));
            }
        }
        //获得当日累计元素及数量
        $keyEliminated = Eliminated::getKeyByType("eliminated", $uid);
        $eliminatedString = $redis->get($keyEliminated);
        if (isset($eliminatedString)) {
            $eliminatedArr = json_decode($eliminatedString, true);
        } else {
            $eliminatedArr = [];
        }
        //获得已完成的每日任务
        $keyDailyFinished = bean(TaskDao::class)->getKeyByType("dailyFinished", $uid);
        $dailyFinishedString = $redis->get($keyDailyFinished);
        $dailyFinishedArr = json_decode($dailyFinishedString, true);
        if (empty($dailyFinishedString)) {
            $dailyFinishedArr = [];
        }
        //将主线任务与日常任务及其进度放入一个数组中
        $taskList = [
            'dailyList' => $dailyToday,
            'dailyFinished' => $dailyFinishedArr
        ];
        //返回数据
        return [
            'code' => 1,
            'taskList' => $taskList,
            'dailyEliminatedList' => $eliminatedArr
        ];
    }

    /**
     * 根据当前关卡与配置文件计算拥有的耗材数
     * @param $level 最高关卡
     * @param $uid 用户uid
     * @param $hatchlings 需要溶剂的hatchling id 列表
     * @return $result 返回拥有的hatchling类型与其对应的个数
     */
    private function getHatchlingByLevel($uid, $level, $hatchlings)
    {
        $hatchlingConfig = Config::loadJson('taskHatchling');
        $result = [];
        //从配置中获得已打过的关卡中积累的hatchling个数
        foreach ($hatchlings as $hatchling) {
            $result[$hatchling] = 0;
            foreach ($hatchlingConfig as $key => $val) {
                if ($key > $level) {
                    break;
                }
                if (array_key_exists($hatchling, $val)) {
                    $result[$hatchling] += $val[$hatchling];
                }
            }
        }
        //已领取过的奖励要减去消耗的hatchling个数
        $masterAllFinished = TaskDao::findAllMaster($uid, true);
        if ($masterAllFinished === null) {
            $masterAllFinished = [];
        }
        $masterConfig = Config::loadJson('task')['task'];
        foreach ($masterAllFinished as $master) {
            if ($master <= 20000 && $master != 10000) {
                continue;
            }
            foreach ($result as $key => $val) {
                if (array_key_exists($key, $masterConfig[$master][0])) {
                    $result[$key] = $val - $masterConfig[$master][0][$key];
                }
            }
        }
        return $result;
    }

    /**
     * 根据目标和现有情况来判断是否达成条件
     * @param array $destination 需要达成的目标列表
     * @param array $completed 今日累计的列表
     * @return bool 是否达成目标
     */
    private function destinationCmp($destination, $completed)
    {
        foreach ($destination as $key => $val) {
            if (!array_key_exists($key, $completed)) {
                return false;
            }
            if ($val > $completed [$key]) {
                return false;
            }
        }
        return true;
    }

    /**
     * 根据所给参数返回一个随机的每日任务ID列表
     * @param numeric $dailyNum 要取的总数或者再要取的个数
     * @param array $had 当今日已经请求过每日活动，但是应为又完成了某些主线导致每日任务变多时，要传递这个参数，已经发送过给客户端的每日任务列表
     * @return array $dailyToday 每日任务列表
     */
    private function getRandomDailyByNum($dailyNum, $had = [])
    {
        $taskConfig = Config::loadJson('task');
        $taskALL = $taskConfig['dailyQuest'];
        $taskTypeALL = array_keys($taskALL);
        shuffle($taskTypeALL);
        $dailyAll = array_keys($taskConfig['dailyQuest']);
        $dailyToday = [];
        $temp = [];
        //先在每一种类型的每日活动中各随机取两个。只有一个的类型就只取一个
        foreach ($taskTypeALL as $key) {
            $tasks = $taskALL[$key];
            $taskKey = array_keys($tasks);
            shuffle($taskKey);
            if (count($tasks) == 1) {
                $temp[$key] = $tasks;
            }
            $tempTaskKey = array_slice($taskKey, 0, 2);
            foreach ($tempTaskKey as $tempKey) {
                $temp[$key][$tempKey] = $tasks[$tempKey];
            }
        }
        //如果当天从来没有获取过日常任务
        if (empty($had)) {
            shuffle($taskTypeALL);
            $diff = $dailyNum - count($temp);
            if ($diff <= 0) {
                //如果diff小于0,说明所需个数小于当前所有类型,随机取出所需数目的类型,这样可保证改情况下每个任务都是不同类型
                $keyTemp = array_slice(array_keys($temp), 0, $dailyNum);
            } else {
                //如果diff>0,说明所需个数大于当前所有类型,直接去除所有类型
                $keyTemp = array_keys($temp);
            }
            //先在每个类型的任务中各取一个任务
            foreach ($keyTemp as $key) {
                foreach ($temp[$key] as $keyTemp => $value) {
                    $dailyToday[$key][] = $keyTemp;
                    break;
                }
                if (count($temp[$key]) == 1) {
                    unset($temp[$key]);
                } else {
                    unset($temp[$key][$keyTemp]);
                }
            }
            //如果所需任务个数多于当前的所有任务类型的个数,需要随机取diff个其他类型的
            if ($diff > 0) {
                $keyTemp = array_slice(array_keys($temp), 0, $diff);
                foreach ($keyTemp as $key) {
                    foreach ($temp[$key] as $keyTemp => $value) {
                        $dailyToday[$key][] = $keyTemp;
                        break;
                    }
                }
            }
        } else {//如果获取过日常任务,
            $count = 0;
            foreach (array_keys($temp) as $key) {
                if ($count >= $dailyNum) {
                    break;
                }
                foreach ($temp[$key] as $keyTemp => $value) {
                    //新的活动不能与之前的重复
                    if (!isset($had[$key]) || !in_array($keyTemp, $had[$key])) {
                        $had[$key][] = $keyTemp;
                        $count++;
                    } else {
                        continue;
                    }
                    break;
                }
            }
            $dailyToday = $had;
        }
        return $dailyToday;
    }

    /**
     * @RequestMapping(route="dailylist")
     * 获取每日任务和每日任务消除元素列表
     * @return array
     */
    public function actionDailyList()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $today = date('Y-m-d', ServerTime::getTestTime());
        $monday = date('Y-m-d', Utils::monday());

        $list = [
            'thisWeek' => [],
            'today' => []
        ];
        foreach ($this->dailyItems as $itemId) {
            $list['today'][$itemId] = EliminatedDaily::getNumberOfToday($itemId);
            $list ['thisWeek'] [$itemId] = EliminatedDaily::getNumberOfWeek($itemId);
        }
        $weekly = TaskDao::findDailyListByType('weekly',$uid,$monday);

        $daily =TaskDao::findDailyListByType('daily',$uid,$today);


        return [
            'code' => 1,
            'eliminatedList' => $list,
            'weeklyRewarded' => $weekly,
            'dailyRewarded' => $daily
        ];
    }

    /**
     * @RequestMapping(route="rewardbytype")
     * 根据奖励类型领取奖励，奖励类型有daily,weekly
     * @param $taskId
     * @param $type
     * @return array
     */
    public function actionRewardByType($taskId, $type)
    {
        $taskConfig = Config::loadJson('dailyTask');
        $taskConfigList = $taskConfig ['tasks'] [$type];
        if (!isset ($taskConfigList [$taskId])) {
            return [
                'code' => 31301,
                'message' => 'task id not in config file!',
                'task_id' => $taskId
            ];
        }

        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $itemId = $taskConfigList [$taskId] [0];
        $target = $taskConfigList [$taskId] [1];
        $completed = 0;
        $today = date('Y-m-d', ServerTime::getTestTime());
        $monday = date('Y-m-d', Utils::monday());
        if ($type == 'daily') {
            $completed = EliminatedDaily::getNumberOfToday($itemId);
        } elseif ($type == 'weekly') {
            $completed = EliminatedDaily::getNumberOfWeek($itemId);
        }

        if ($completed < $target) {
            return [
                'code' => 31302,
                'message' => 'task target not complete!'
            ];
        }

        if ($type == 'daily') {
            $task = TaskDao::findUsedInRewardByType($type,$uid,$taskId,$today);//返回array

        } elseif ($type == 'weekly') {
            $task = TaskDao::findUsedInRewardByType($type,$uid,$taskId,$monday);//返回count
        }
        if (empty($task) || $task == 0) {
            $task = new Task ();
            $task->setUid($uid);
            $task->setTaskId($taskId);
            $task->setRewardTime(strtotime($today));
            $task->setCreatedAt(time());
            $task->setUpdatedAt(time());
            $task->save()->getResult();
        } else {
            return [
                'code' => 31303,
                'message' => 'task has been rewarded!',
                'task_id' => $taskId
            ];
        }

        $rewardType = $taskConfigList [$taskId] [2];
        $rewardNum = $taskConfigList [$taskId] [3];
        if (in_array($rewardType, [
            'gold',
            'silver',
            'live'
        ])) {
            $moneyType = $rewardType == "gold" ? 1279 : ($rewardType == "silver" ? 1280 : 1278);
            bean(PropertyService::class)->handleOne($moneyType,$rewardNum,FlowActionConst::DETAIL_DAILY_TASK_REWARD,$taskId);
        } else {
            bean(PropertyService::class)->handleOne($rewardType,$rewardNum,FlowActionConst::DETAIL_DAILY_TASK_REWARD,$taskId);

        }

        return [
            'code' => 1,
            'type' => $rewardType,
            'number' => $rewardNum
        ];
    }

    /**
     * 签到并领取奖励接口
     * @RequestMapping(route="sign")
     */
    public function actionSign()
    {

        $signService = bean(SignService::class);

        if ($signService->isSigned()) {
            return $this->returnError(Message::SIGNED_TODAY);
        }

        //读取奖励配置
        $rewardConfig = $signService->getRewardConfig();

        $signedDays = $signService->getSignedDays();
        $signOffset = $signService->getSignOffset();

        //到最大天数则重置签到周期,重置奖励配置
        if ($signOffset > $signService::CYCLE_DAYS) {
            $signedDays = 0;
            $signService->flushSignInfo();
            $rewardConfig = $signService->resetRewardConfig();
        }


        $reward = isset($rewardConfig[$signedDays]) ? $rewardConfig[$signedDays] : [];


        //随机抽奖
        $sendReward = true;
        if (count($reward) != count($reward, 1)) {
            $sendReward = false;
        }

        //发奖
        if ($signService->sign() && $sendReward === true) {
            //平台特权多领20金币
            if (UserInfoDao::isPlatformVip()) {
                $platformRewardCfg = Config::loadJson('platformRewardCfg');
                $extraSilver = $platformRewardCfg['qq_start']['extraDailySignSilver'];//目前qq微信奖励相同，全使用qq
                $reward[1280] = isset($reward[1280]) ? $reward[1280] + $extraSilver : $extraSilver;
            }
            bean(PropertyService::class)->handleBatch($reward,FlowActionConst::DETAIL_DAILY_SIGN,$signedDays + 1);

        }

        return $this->returnData([
            'rewardDetail' => $reward,
            'resetSeconds' => $signService->getResetSeconds()
        ]);
    }


    /**
     * 补签
     * @return array|bool
     * @RequestMapping(route="redosign")
     */
    public function actionRedoSign()
    {

        $signService = bean(SignService::class);

        $signOffset = $signService->getSignOffset();
        $signedDays = $signService->getSignedDays();
        if ($signOffset > SignService::CYCLE_DAYS || $signOffset == $signedDays) {
            return $this->returnError(Message::REDO_SIGN_FAIL);
        }

        //读取奖励配置
        $rewardConfig = $signService->getRewardConfig();
        $reward = isset($rewardConfig[$signedDays]) ? $rewardConfig[$signedDays] : [];
        $sendReward = true;
        if (count($reward) != count($reward, 1)) {
            $sendReward = false;
        }

        if ($signService->redoSign() && $sendReward === true) {
            //平台特权多领20金币
            if (UserInfoDao::isPlatformVip()) {
                $platformRewardCfg = Config::loadJson('platformRewardCfg');
                $extraSilver = $platformRewardCfg['qq_start']['extraDailySignSilver'];//目前qq微信奖励相同，全使用qq
                $reward[1280] = isset($reward[1280]) ? $reward[1280] + $extraSilver : $extraSilver;
            }
            bean(PropertyService::class)->handleBatch($reward,FlowActionConst::DETAIL_DAILY_SIGN,$signedDays + 1);
            bean(PropertyService::class)->handleOne(MoneyType::GOLD,-1 * SignService::REDO_SIGN_COST,0,"");
        }

        return $this->returnData([
            'rewardDetail' => $reward
        ]);
    }


    /**
     * 获取配置
     * @RequestMapping(route="getconfig")
     */
    public function actionGetConfig($name = '')
    {
        $ret = [];

        if ($name == 'dailySignReward') {
            $signService = bean(SignService::class);
            $ret = $signService->getRewardConfig();
        } else {
            if (!empty($name)) {
                $ret = Config::loadJson($name);
            }
        }

        return [
            'code' => 1,
            'config' => $ret
        ];
    }

    /**
     * @RequestMapping(route="signinfo")
     * @return array
     */
    public function actionSignInfo()
    {

        $signService = bean(SignService::class);
        $ret = $signService->getSignInfo();
        return [
            'code' => 1,
            'info' => $ret
        ];
    }

    /**
     * 发放或者获取无限生命时间
     * @RequestMapping(route="unlimitedlife")
     */
    public function actionUnlimitedLife($id = null)
    {
        $result = TaskDao::setUnlimitedLife($id);
        if ($result === false) {
            return [
                "code" => 31306,
                "message" => "id not exist!"
            ];
        }
        return [
            "code" => 1,
            "residualTime" => $result
        ];
    }

    /**
     * 按照概率取奖励类容
     * @param array $rewards [
     *    ["1543",1,50],
     *    ["1279",20,2.5],
     *    ["1279",30,2.5],
     *    ["1279",50,1],
     *    ["1536",2,10],
     *    ["1284",2,10],
     *    ["1282",2,6],
     *    ["1283",2,6],
     *    ["1281",3,12]
     *   ]
     */
    private function getRewardByRank($rewards)
    {
        $randNum = rand(1, 100);
        $var = 0;
        $reward = [];
        ArrayHelper::multisort($rewards, 2, SORT_DESC);
        foreach ($rewards as $key => $item) {
            $itemId = $item[0];
            $itemNum = $item[1];
            $probability = $item[2];
            if ($randNum > $var && $randNum <= ($var += $probability)) {
                break;
            }
        }

        if (isset($itemId) && isset($itemNum)) {
            $reward[$itemId] = $itemNum;
        }

        return $reward;
    }

    /**
     * 获取任务进度
     * @RequestMapping(route="getprogress")
     */
    public function actionGetProgress()
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $redis = bean('redis');
        $redisKey = sprintf(RedisKey::TASK_PROGRESS, $uid);
        $progress = $redis->get($redisKey);
        return [
            'code' => 1,
            'task_progress' => $progress
        ];
    }

    /**
     * 保存任务进度
     * @RequestMapping(route="uploadprogress")
     */
    public function actionUploadProgress($progress)
    {
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $redis = bean('redis');
        $redisKey = sprintf(RedisKey::TASK_PROGRESS, $uid);
        $redis->set($redisKey, $progress);
        return [
            'code' => 1,
        ];
    }
}