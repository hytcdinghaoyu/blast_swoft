<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class LoginLog extends DataLog
{

    public $topLevel = 0;//之前版本传的参数，目前tlog没有此参数

    /**
     * (必填)剩余星星数
     * @var int
     */
    public $totalStar = 0;

    /**
     * (必填)剩余金币数
     * @var int
     */
    public $Gold = 0;

    /**
     * (必填)剩余银币数
     * @var int
     */
    public $Silver = 0;

    /**
     * (必填)剩余关前炸弹道具数
     * @var int
     */
    public $CountBoom = 0;

    /**
     * (必填)剩余关前加三步道具数
     * @var int
     */
    public $CountThreeStep = 0;

    /**
     * (必填)剩余弹弓道具数
     * @var int
     */
    public $CountSingle = 0;

    /**
     * (必填)剩余横向火箭道具数
     * @var int
     */
    public $CountRow = 0;

    /**
     * (必填)剩余竖向火箭道具数
     * @var int
     */
    public $CountColumn = 0;

    /**
     * (必填)剩余置换道具数
     * @var int
     */
    public $CountRefresh = 0;

    /**
     * (必填)剩余镭射枪道具数
     * @var int
     */
    public $CountColor = 0;

    /**
     * (必填)繁荣度
     * @var int
     */
    public $Prosperity = 0;

    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

}