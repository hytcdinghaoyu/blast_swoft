<?php

namespace App\Constants;

/**
 *
 * 两种邀请奖励
 * @package app\common\constants
 */
final class InviteRewardType extends BasicEnum
{

    //邀请
    const EACH_INVITE = "eachInvite";

    //邀请进度奖励领取
    const PROGRESS_REWARD = "progressReward";

    //每天邀请上限
    const DAILY_LIMIT = 3;


}