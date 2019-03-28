<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class CollectLog extends DataLog
{

    public $type = '';//varchar

    public $catalog = '';//varchar

    public $code = '';

    public $msg = '';//varchar


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}