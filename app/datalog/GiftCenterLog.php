<?php

namespace App\datalog;
use yii\helpers\ArrayHelper;

class GiftCenterLog extends DataLog
{


    public $AreaId = 0;

    public $PlatId = 0;

    public $OpenId = '';

    public $RewardId = 0;

    public $RewardContent = '';

    public $Source = 0;

    public $Serial = '';



    public function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);
    }



}