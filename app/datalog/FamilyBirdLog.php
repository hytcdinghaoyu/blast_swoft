<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class FamilyBirdLog extends DataLog
{

    public $birdId = '';

    public $show = 0;

    public $pos = '';


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}