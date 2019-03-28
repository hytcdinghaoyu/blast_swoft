<?php
namespace App\Models\Dao;


use App\Models\Entity\Rule;
use App\Models\Entity\RuleConfigurations;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use yii\helpers\ArrayHelper;

/**
 *
 * @Bean()
 */
class RuleConfigurationsDao
{
    public static function getValueByRuleId($ruleId)
    {
        $configs = Query::table(RuleConfigurations::class)->condition(['ruleId' => $ruleId])->get([
            'type',
            'value',
            'md5'
        ])->getResult();

        if (empty($configs)) {
            return [];
        }
        foreach ($configs as $key => $config) {
            $configs[$key]['type'] = static::type2StringMap($config['type']);
            if ($config['type'] == 1) {
                unset($configs[$key]['md5']);
            }
        }
        return $configs;
    }

    private static function type2StringMap($type)
    {
        $map = [
            '1' => 'json',
            '2' => 'filePath'
        ];
        return $map[$type];
    }
}