<?php

namespace App\Controllers\Api;

use App\datalog\FamilyBirdLog;
use app\datalog\FamilyQuestLog;
use App\Models\GardenRewardFlag;
use App\Models\Dao\FamilyBirdsDao;
use App\Models\Dao\FamilyQuestDao;
use App\Constants\FlowActionConst;
use App\Models\Dao\UserInfoDao;
use App\Utils\Config;
use App\Services\PropertyService;
use App\Controllers\CommonController;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;

/**
 * 用户模块.
 * @Controller(prefix="/family")
 */
class FamilyController extends CommonController
{

    /**
     * @RequestMapping(route="getinfo")
     * 获取制定用户所有家园信息
     */
    public function actionGetInfo($uid)
    {
        $birds = array_map(function ($value) {
            return [
                $value['birdId'],
                $value['show'],
            ];
        }, FamilyBirdsDao::getInfo($uid));
        $quests = array_map(function ($value) {
            return [
                $value['questId'],
                $value['state'],
                $value['step'],
                $value['own_steps'],
                $value['init_play'],
                $value['show'],
                $value['draw'],
                $value['s_time'],
            ];
        }, FamilyQuestDao::getInfo($uid));
        return [
            "code" => 1,
            "birds" => $birds,
            "quests" => $quests
        ];
    }

    /**
     * @RequestMapping(route="update")
     * 更新用户家园信息
     */
    public function actionUpdate($fields)
    {
        $globalInfo= globalInfo();
        bean(UserInfoDao::class)->setDailyActive();

        $uid = $globalInfo->getUid();
        $errors = [];
        if (isset($fields['birds'])) {
            foreach ($fields['birds'] as $bRecord) {
                $bRecord['uid'] = $uid;
                if (($error = FamilyBirdsDao::updateRecord($bRecord)) !== true) {
                    $errors [] = $error;
                } else {
                    $logBirdData = $bRecord;
                    $familyBirdLog = new FamilyBirdLog();
                    $familyBirdLog->mergeClientLog($logBirdData);
                    $familyBirdLog->send();
                }
            }
        }
        if (isset($fields['quests'])) {
            foreach ($fields['quests'] as $qRecord) {
                $qRecord['uid'] = $uid;
                if (($error = FamilyQuestDao::updateRecord($qRecord)) !== true) {
                    $errors [] = $error;
                } else {
                    $logQuesData = $qRecord;
                    $familyQuestLog = new FamilyQuestLog();
                    $familyQuestLog->mergeClientLog($logQuesData);
                    $familyQuestLog->send();
                }

            }
        }
        if (!empty($errors)) {
            return [
                'code' => 39001,
                'message' => $errors
            ];
        }
        return [
            'code' => 1
        ];
    }

    /**
     * @RequestMapping(route="reward")
     * 领取奖励
     */
    public function actionReward($rewardId)
    {
        $config = Config::loadJson('gardenEventReward');
        $globalInfo= globalInfo();
        foreach ($config as $item) {
            $rewardIdArr = array_keys($item);
            $gardenRewarded = GardenRewardFlag::findOne(['uid' => $globalInfo->getUid(), 'rewardId' => $rewardId]);
            //从未领过此次奖励
            if (in_array($rewardId, $rewardIdArr) && !$gardenRewarded) {
                $reward = $item[$rewardId];
                bean(PropertyService::class)->handleBatch($reward,FlowActionConst::ACTION_BANISH_PIG,date('Y-m-d'));


                //标记已经领取过了

                    $gardenRewarded = new GardenRewardFlag();
                    $gardenRewarded->uid = $globalInfo->getUid();
                    $gardenRewarded->rewardId = $rewardId;
                    $gardenRewarded->save();


                return ['code' => 1];
            }

        }
        return ['code' => 20005, 'msg' => 'data error'];

    }

    /**
     * @RequestMapping(route="getreward")
     *
     */

    public function actionGetReward($id)
    {
        $globalInfo = globalInfo();
        $ret =  GardenRewardFlag::findOne(['uid' => $globalInfo->getUid(), 'rewardId' => $id]);

        return [
            'code' => 1,
            'rewarded' => $ret ? 1 : 0
        ];
    }

    /**
     * @RequestMapping(route="chapterrewarded")
     *
     */

    public function actionChapterRewarded($chapterId = 0)
    {

        $globalInfo = globalInfo();
        $uuid = $globalInfo->getUuid();
        $chapterRewarded = FamilyQuestDao::getChapterReward($uuid, $chapterId);

        return [
            'code' => 1,
            'rewarded' => $chapterRewarded ? 1 : 0,
        ];
    }
}
