<?php
namespace App\datalog;

use yii\helpers\ArrayHelper;
/**
 * Class AppStart
 * 新客户端打开流水表
 * @package app\datalog
 */
class MTLeagueScore extends DataLog {

    public $level = 0;

    public $win = 0;

    public $score = 0;

    public $star = 0;

    public $map_version = 0;

    public $map_name = '';

    public $items_count = 0;

    public $step = '';

    public $duration = 0;

    public $counts = 0;

    public $silver = 0;

    public $gold = 0;

    public $lives = 0;

    public $logId = '';

    public $isFirstPass = 0;

    public $difficult = 0;

    public $totalScore = 0;

    public $totalStar = 0;

    public $map_id = 0;

    public $topLevel = 0;

    public $leagueSeason = 0;

    public $rankDan = '';

    public $leagueUserGroup = '';

    public $clientRankDan = 0;

    public $clientIsRich = 0;

    public $clientGroupNumber = 0;

    function rules()
    {
        $rules = [];
        return ArrayHelper::merge(parent::rules(), $rules);

    }
}