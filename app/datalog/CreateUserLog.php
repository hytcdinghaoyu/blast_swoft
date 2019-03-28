<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class CreateUserLog extends DataLog
{

    public $db = '';

    public $username = '';

    public $customerId = '';

    public $createtime = 0;

    public $logId = '';


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}