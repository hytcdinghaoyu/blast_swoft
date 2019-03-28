<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2018/6/6
 * Time: 下午2:47
 */

namespace App\Controllers\Api;

use App\Controllers\CommonController;
use App\Constants\FlowActionConst;
use App\Constants\Message;
use App\Services\NewUserActivityService;
use App\Services\PropertyService;
use App\Utils\Config;
use App\Utils\ServerTime;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;

/**
 * 用户模块.
 * @Controller(prefix="newuseractivity")
 */

class NewUserActivityController extends CommonController
{
    /**
     *
     *@RequestMapping(route="getstatus")
     */


    public function actionGetStatus()
    {
        $new_user_activity = bean(NewUserActivityService::class);
        $remain_time = $new_user_activity->remainTime();
        $status = ($remain_time == 0) ? false : true;

        return [
            'code' => 1,
            'status' => $status,
            'remain_time' => $remain_time
        ];
    }

    /**
     *
     *@RequestMapping(route="getinfo")
     */


    public function actionGetInfo()
    {
        //未开始则创建
        $new_user_activity = bean(NewUserActivityService::class);
        $record = $new_user_activity->creatIfNew();
        $remain_time = $record->getCreatedAt() + $new_user_activity->activity_time -  ServerTime::getTestTime();
        $current_day = ceil((ServerTime::getTestTime() - strtotime(date('Y-m-d', $record->getCreatedAt()))) / 86400);
        $config = Config::loadJson('newUserActivity');
        $rewardList = $new_user_activity->rewardedList();

        //normal部分
        foreach ($config['normal'] as $key => &$value) {
            if (in_array('normal-' . $key, $rewardList)) {
                $value['is_rewarded'] = true;
            }
            if ($current_day >= $value['day']) {
                $value['is_opened'] = true;
            }
        }
        //大礼包部分
        foreach ($config['score_reward'] as $key => &$item) {
            if (in_array('score_reward-' . $key, $rewardList)) {
                $item['is_rewarded'] = true;
            }
        }

        return [
            'code' => 1,
            'config' => $config,
            'remain_time' => $remain_time,
            'total_score' => $new_user_activity->getScore()
        ];
    }

    /**
     *
     *@RequestMapping(route="reward")
     */

    public function actionReward($reward_type, $reward_id)
    {

        $new_user_activity = bean(NewUserActivityService::class);

        $config = Config::loadJson('newUserActivity');

        /**
         * 任务结束奖励
         */
        if ($reward_type == 'score_reward' && isset($config[$reward_type][$reward_id])) {
            $reward_config = $config[$reward_type][$reward_id];
            $total_score = $new_user_activity->getScore();
            if ($new_user_activity->isRewarded($reward_type . '-' . $reward_id)) {
                return [
                    'code' => Message::HAS_REWARDED,
                    'msg' => Message::getMessage(Message::HAS_REWARDED),
                    'total_score' => $total_score
                ];
            }
            //满足结束奖励的分数要求才能领取
            if ($total_score >= $reward_config['need_score']) {
                $reward = $new_user_activity->filterSkinItem($reward_config['reward']);
                bean(PropertyService::class)->handleBatch($reward,FlowActionConst::NEW_USER_ACTIVITY,'');
                $new_user_activity->tagReward($reward_type . '-' . $reward_id);

                if ($new_user_activity->is_completed($config[$reward_type])) {
                    $new_user_activity->complete();
                }


                return [
                    'code' => 1,
                    'total_score' => $total_score,
                    'reward' => $reward_config['reward']
                ];
            }
            return [
                'code' => Message::SCORE_NOT_ENOUGH,
                'msg' => Message::getMessage(Message::SCORE_NOT_ENOUGH),
                'total_score' => $total_score
            ];
        }

        /**
         * 每日奖励
         */
        if ($reward_type == 'normal' && isset($config[$reward_type][$reward_id])) {
            $normal_config = $config['normal'][$reward_id];
            if ($new_user_activity->isRewarded($reward_type . '-' . $reward_id)) {
                $total_score = $new_user_activity->getScore();
                return [
                    'code' => Message::HAS_REWARDED,
                    'msg' => Message::getMessage(Message::HAS_REWARDED),
                    'total_score' => $total_score
                ];
            }

            $reward = $new_user_activity->filterSkinItem($normal_config['reward']);
            bean(PropertyService::class)->handleBatch($reward,FlowActionConst::NEW_USER_ACTIVITY,'');
            $new_user_activity->tagReward($reward_type . '-' . $reward_id);
            $total_score = $new_user_activity->incrScore($normal_config['score']);
            return [
                'code' => 1,
                'total_score' => $total_score,
                'reward' => $reward
            ];
        }
        return [
            'code' => Message::INVALID_REWARD_ID,
            'msg' => Message::getMessage(Message::INVALID_REWARD_ID)
        ];


    }

}