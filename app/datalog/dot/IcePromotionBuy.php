<?php

namespace App\datalog\dot;

use App\datalog\DataLog;
use yii\helpers\ArrayHelper;

/**
 * Class IcePromotionBuy
 * @package app\datalog\dot
 */
class IcePromotionBuy extends DataLog
{

    public $diamond = 0;

    public $paymentId = '';//付款Id

    public $localItems = 0;

}