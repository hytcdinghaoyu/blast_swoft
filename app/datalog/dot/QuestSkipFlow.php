<?php

namespace App\datalog\dot;

use App\datalog\DataLog;
use yii\helpers\ArrayHelper;
/**
 * Class QuestGuideFlow
 * 剧情对话跳过流水表(剧情对话点击skip触发日志)
 * @package app\datalog
 */
class QuestSkipFlow extends DataLog
{

    public $QuestID = '';

    public $DialogueSkip = '';

}