<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class ItemsLog extends DataLog
{

    public $map_id = 0;

    public $map_version = 0;

    public $map_name = '';

    public $customer_id = 0;

    public $level = 0;

    public $is_win = 0;

    public $item_id = 0;

    public $number = 0;

    public $AfterCount = 0;

    public $e_time = '';

    public $difficult = 0;

    public $is4Star = 0;

    public $flowAction = 0;

    public $model = '';

    public $BattleID = 0;

    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}