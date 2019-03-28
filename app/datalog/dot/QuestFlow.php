<?php

namespace App\datalog\dot;

use App\datalog\DataLog;
use yii\helpers\ArrayHelper;
/**
 * Class QuestFlow
 * 剧情任务流水表
 * @package app\datalog
 */
class QuestFlow extends DataLog
{

    public $QuestID = '';

    public $Questlevel = 0;

    public $QuestType = 0;

    public $star = 0;

    public $Gold = 0;

    public $Level = 0;

    public $Silver = 0;

    public $CountBuySkin = 0;


}