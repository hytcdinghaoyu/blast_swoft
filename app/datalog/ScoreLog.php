<?php

namespace App\datalog;

use yii\helpers\ArrayHelper;

class ScoreLog extends DataLog
{

    public $level = 0;

    /**
     * 对局结果
     * @var int
     */
    public $win = 0;

    public $score = 0;

    public $star = 0;

    public $total_star = 0;

    public $map_version = 0;

    //关卡包类型
    public $map_name = '';

    /**
     * 使用的道具总数
     * @var int
     */
    public $items_count = 0;

    /**
     * 剩余步数
     * @var int
     */
    public $step = 0;

    /**
     * 关卡消耗时长
     * @var int
     */
    public $duration = 0;

    public $counts = 0;

    public $silver = 0;

    public $gold = 0;

    public $lives = 0;

    /**
     * 是否首次过关
     * @var int
     */
    public $isFirstPass = 0;

    public $difficult = 0;

    public $totalScore = 0;

    public $is4Star = 0;

    public $map_id = 0;

    public $topLevel = 0;

    //剩余总星星数
    public $totalStar = 0;

    //对局唯一ID，用来关联itemsLog中的battleItem
    public $BattleID = 0;

    //过关剩余步数，未过关时记为-1
    public $SurplusStep = 0;

    //关卡目标总数
    public $LevelGoals = 0;

    //关卡目标进度数
    public $BattleGoals = 0;
    
    //排名
    public $Rank = 0;
    
    public function rules()
    {
        $rules = [
            [['level', 'win', 'score'], 'integer']
        ];
        return ArrayHelper::merge(parent::rules(), $rules);
    }

    

}