<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class RequestErrorLog extends DataLog
{

    public $type = '';

    public $code = '';

    public $message = '';

    public $action = '';

    public $clientTime = 0;

    public $param = '';


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}