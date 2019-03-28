<?php
namespace App\datalog;

use yii\helpers\ArrayHelper;
/**
 * Class AppStart
 * 新客户端打开流水表
 * @package app\datalog
 */
class MTLeagueLogin extends DataLog {

    public $leagueSeason = 0;

    public $rankDan = '';

    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}