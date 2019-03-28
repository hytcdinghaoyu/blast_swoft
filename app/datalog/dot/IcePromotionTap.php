<?php

namespace App\datalog\dot;

use App\datalog\DataLog;
use yii\helpers\ArrayHelper;

/**
 * Class IcePromotionTap
 * @package app\datalog\dot
 * 破冰促销点击
 */
class IcePromotionTap extends DataLog
{

    public $diamond = 0;

    public $paymentId = '';//付款Id

    public $localItems = 0;

}