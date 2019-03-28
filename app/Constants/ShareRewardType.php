<?php

namespace App\Constants;

/**
 *
 * 三种分享奖励
 * @package app\common\constants
 */
final class ShareRewardType extends BasicEnum
{
    //普通过关
    const NORMAL_LEVEL = 'normalLevel';

    //特殊过关
    const SPECIAL_LEVEL = 'specialLevel';

    //新的一天
    const NEW_DAY = 'newDay';

    //区域完成
    const AREA_FINISH = 'areaFinish';

    //超越分享
    const EXCEED = 'exceed';

}