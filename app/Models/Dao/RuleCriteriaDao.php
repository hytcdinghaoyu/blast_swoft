<?php
namespace App\Models\Dao;


use App\Models\Entity\Rule;
use App\Models\Entity\RuleCriteria;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use yii\helpers\ArrayHelper;

/**
 *
 * @Bean()
 */
class RuleCriteriaDao
{
    public static $allChoices = [
        '=',
        '>',
        '<',
        '!=',
        '>=',
        '<='
    ];


    public static function getCriteriasByRuleId($ruleId)
    {
        $all = Query::table(RuleCriteria::class)->condition(['ruleId' => $ruleId])->get()->getResult();
        return $all;
    }
}