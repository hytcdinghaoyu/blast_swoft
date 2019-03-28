<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class DigPigLog extends DataLog
{

// 消除猪维护每日和每周任务
    public $Item_pig = 0;

    public $Item_big_pig = 0;

    public $giveUp = '';


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}