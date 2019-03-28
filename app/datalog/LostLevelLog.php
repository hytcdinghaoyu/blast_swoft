<?php

namespace App\datalog;
use yii\helpers\ArrayHelper;

class LostLevelLog extends DataLog
{

    public $lostLevel = 0;


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}