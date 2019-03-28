<?php
namespace App\datalog;

use yii\helpers\ArrayHelper;
/**
 * Class AppStart
 * 新客户端打开流水表
 * @package app\datalog
 */
class LeagueChestReward extends DataLog {

    public $rewardType = '';

    public $taskId = '';

    public $model = '';

    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }
}