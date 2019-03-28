<?php

namespace app\datalog;

use yii\helpers\ArrayHelper;

class FamilyQuestLog extends DataLog
{

    public $questId = '';//varchar

    public $step = '';//varchar

    public $show = 0;

    public $state = 0;//varchar

    public $draw = 0;

    public $owns_step = '';//varchar

    public $init_play = 0;

    public $s_time = 0;

    public $useStar = 0;


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}