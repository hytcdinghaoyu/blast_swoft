<?php

namespace App\datalog;

//use app\tlog\TLog;
use yii\helpers\ArrayHelper;

class LogoutLog extends DataLog
{

    public $OnlineTime = 0;
    /**
     * (必填)等级
     * @var int
     */
    public $Level = 0;

    /**
     * (必填)玩家好友数量
     * @var int
     */
    public $PlayerFriendsNum = 0;


    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }



}