<?php

namespace App\Controllers\Cloud;

use App\Constants\LeaderBoardType;
use App\Constants\Message;
use App\Controllers\CommonController;
use App\Models\Dao\RuleDao;
use App\Services\HotUpdateService;
use Swoft\Bean\Annotation\Inject;
use App\Services\LeaderBoardService;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;


/**
 * 排行榜服务.
 *
 * @Controller(prefix="/hotupdate")
 */
class HotUpdateController extends CommonController
{
    /**
     *
     * @RequestMapping(route="update")
     */
    public function actionUpdate()
    {
        $commonVar = commonVar();
        if($commonVar->getUuid() == '') {
           // \Yii::$app->globalInfo->uuid = 'uuid-null';
        }
        /**
         * 拿到分好组（父级子级）的规则
         */
        $rules = RuleDao::getAllGroupRuleByAppId();
        if ($rules === false) {
            return [
                'code' => Message::HOTUPDATE_RULE_ERROR
            ];
        }
        /**
         * 查询到匹配的规则，并拿到匹配的规则的文件
         */
        $contain = [];
        $ruleId = [];
        foreach ($rules as $rule) {
            $data = bean(HotUpdateService::class)->matchGroupRules($rule);
            if (!empty($data['contain'])) {
                $contain[] = $data['contain'];
            }
            if (!empty($data['matchId'])) {
                $ruleId[] = $data['matchId'];
            }
        }

        /**
         * 将拿到的文件整理好返回
         */
        return [
            'code' => 1,
            'contain' => $contain
        ];

    }
}