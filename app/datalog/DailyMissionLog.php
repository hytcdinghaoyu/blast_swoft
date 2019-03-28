<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class DailyMissionLog extends DataLog
{

    public $day = '';

    public $action = '';


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}