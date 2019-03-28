<?php

namespace App\Tasks;

use App\Models\Entity\RuleConfigurations;
use App\Constants\RedisKey;
use App\Models\Entity\Rule;
use App\Models\Entity\RuleCriteria;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Task\Bean\Annotation\Task;
use Swoft\Task\Bean\Annotation\Scheduled;
use Swoft\Db\Query;
use Swoft\App;


/**
 * Sync task
 *
 * @Task("Rule")
 */
class RuleTask

{
    /**
     * 导入热更新规则
     * @Scheduled(cron="3 * * * * *")
     */
    public function importHotUpdate()
    {
        ConsoleUtil::log('Start ' . __FUNCTION__ . " pid : " . getmypid());
        //获取读取过的文件列表，以file_md5标识
        $redis = bean('redis');
        $list = $redis->SMEMBERS(RedisKey::HOT_UPDATE_PROCESSED);

        $json_files = glob(App::getAlias('@res/hotUpdate/*.json'));
        foreach ($json_files as $file) {

            $file_md5 = md5_file($file);
            if (!in_array($file_md5, $list)) {
                $json = json_decode(file_get_contents($file), true);
                if ($json['type'] != 'hotUpdate') {
                    return;
                }
                if ($json['operation'] == 'insert') {
                    $this->insert_rule($json);
                } elseif ($json['operation'] == 'update') {
                    $this->delete_rule($json);
                    $this->insert_rule($json);
                } elseif ($json['operation'] == 'delete') {
                    $this->delete_rule($json);
                }
                $redis->SADD(RedisKey::HOT_UPDATE_PROCESSED, $file_md5);
            }
        }
    
        ConsoleUtil::log('End ' . __FUNCTION__);
    }

    private function insert_rule($json)
    {
        if (!isset($json['rule'])) {
            return false;
        }

        $rule = new Rule();
        $json['rule']['updated_at'] = time();
        $json['rule']['created_at'] = time();
        $rule->fill($json['rule']);
        if (!$rule->save()->getResult()) {
            return false;
        }
        $ruleId = $rule->getRuleId();

        if (isset($json['rule_criteria']) && is_array($json['rule_criteria'])) {
            foreach ($json['rule_criteria'] as $value) {
                $rule_criteria = new RuleCriteria();
                $rule_criteria->setRuleId($ruleId);
                $rule_criteria->fill($value);
                $rule_criteria->save()->getResult();
            }
        }
        if (isset($json['rule_configurations']) && is_array($json['rule_configurations'])) {
            foreach ($json['rule_configurations'] as $value) {
                $rule_configurations = new RuleConfigurations();
                $rule_configurations->setRuleId($ruleId);
                $value['updated_at'] = time();
                $value['created_at'] = time();
                if (empty($value['md5'])){
                    $value['md5'] = 'empty';
                }
                $rule_configurations->fill($value);
                $rule_configurations->save()->getResult();
            }
        }
    }

    private function delete_rule($json)
    {
        if (!isset($json['rule']['ruleName'])) {
            return false;
        }
        $ruleName = $json['rule']['ruleName'];
        $rule = Query::table(Rule::class)->condition(['ruleName' => $ruleName])->one()->getResult();
        if (empty($rule)) {
            return false;
        }
        $ruleId =$rule['ruleId'];
        Rule::deleteAll(['ruleId' => $ruleId])->getResult();
        RuleCriteria::deleteAll(['ruleId' => $ruleId])->getResult();
        RuleConfigurations::deleteAll(['ruleId' => $ruleId])->getResult();
    }

}