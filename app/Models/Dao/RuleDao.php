<?php
namespace App\Models\Dao;


use App\Models\Entity\Rule;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use yii\helpers\ArrayHelper;

/**
 *
 * @Bean()
 */
class RuleDao
{
    // 规则缓存时间，暂定五分钟
    const RULE_CACHE_TIME = 300;

    const STATUS_ON = 1;

    const STATUS_OFF = 2;

    const STATUS_ARCHIVED = 3;

    const ISPARENT_YES = 1;

    const ISPARENT_NO = 0;
    // 是顶级规则
    const PARENT_RULE_TOP = 0;


    /**
     * 通过AppId获得所有分好组的规则
     */
    public static function getAllGroupRuleByAppId()
    {
        $originDatas = static::getAllOnRuleByAppId();
        if (empty($originDatas)) {
            return [];
        }
        try {
            $nodes = ArrayHelper::index($originDatas, null, 'isParent');
            $groupItems = [];
            foreach ($nodes[0] as $node) {
                $item = [];
                $item[] = $node;
                if ($node['parentRule'] != static::PARENT_RULE_TOP) {
                    $parent = ArrayHelper::getValue($originDatas, function ($list, $id) {
                        foreach ($list as $li) {
                            if ($li['ruleId'] == $id) {
                                return $li;
                            }
                        }
                    }, $node['parentRule']);
                    $item[] = $parent;
                }
                $groupItems[] = $item;
            }
        } catch (\Exception $e) {
            return false;
        }
        return $groupItems;
    }

    /**
     * 通过AppId从数据库中获取所有开启的规则
     */
    public static function getAllOnRuleByAppId()
    {
        $all = Query::table(Rule::class)->condition(['status' => static::STATUS_ON])->get()->getResult();
        return $all;
    }
}