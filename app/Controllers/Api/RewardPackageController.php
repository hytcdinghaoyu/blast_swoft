<?php

namespace App\Controllers\Api;

use App\Controllers\CommonController;;
use App\Models\Dao\CenterBulletinBoardDao;
use App\Models\Dao\CenterRewardPackageDao;
use App\Models\Dao\TaskDao;
use App\Services\PropertyService;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Constants\FlowActionConst;



/**
 * 用户模块.
 * @Controller(prefix="/rewardpackage")
 */
class RewardPackageController extends CommonController
{
    /**
     * @RequestMapping(route="recieve")
     * 领取包奖励
     */
    public function actionRecieve($rewardId, $origin, $fatherId = 0)
    {
        if (!in_array($origin,
            [FlowActionConst::DETAIL_BULLETIN_BOARD, FlowActionConst::DETAIL_CONTINUE_FAIL_REWARD])
        ) {
            return [
                "code" => 50001,
                "message" => "$origin unknown origin!"
            ];
        }

        /**
         * 查询该奖励包每个人的奖励次数
         */
        $reward = bean(CenterRewardPackageDao::class)->getById($rewardId);
        if (empty($reward)) {
            return [
                "code" => 50002,
                "message" => "$rewardId reward not found!"
            ];
        }

        /**
         * 可领取的次数
         */
        $recieveNum = $reward["recieve_num"];

        /**
         * 查询已领取的次数
         */
        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        $recievedNum = bean(TaskDao::class)->getCountById($uid, $rewardId);
        if ($recievedNum >= $recieveNum) {
            return [
                "code" => 50005,
                "message" => "recieved num have exceeded $recieveNum!"
            ];
        }
        if ($fatherId !== 0) {
            //查询父级中奖励是否还有效
            if ($origin == FlowActionConst::DETAIL_BULLETIN_BOARD) {
                $bulletin = bean(CenterBulletinBoardDao::class)->getById($fatherId);
                if (empty($bulletin) || $bulletin['is_actived'] == 0) {
                    return [
                        "code" => 50003,
                        "message" => "the bulletin is invalid!"
                    ];
                }
                if ($bulletin['reward_id'] != $rewardId) {
                    return [
                        "code" => 50004,
                        "message" => "the rewardId is not same with it's bullet's!"
                    ];
                }
            }
        }

        /**
         * 发放奖励
         */
        $rewardPackage = json_decode($reward['contain_list'], true);
        bean(PropertyService::class)->handleBatch($rewardPackage, $origin,$rewardId);
        bean(TaskDao::class)->createOne($uid,$rewardId);
        bean(TaskDao::class)->delCache($uid);

        return [
            "code" => 1,
            "reward_detail" => $rewardPackage
        ];
    }
}