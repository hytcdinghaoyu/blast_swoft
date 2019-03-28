<?php

namespace App\Services;

use App\Constants\ActivityType;
use App\Constants\RedisKey;
use App\Models\Dao\CenterActivityDao;
use App\Models\Dao\RuleAttributeDao;
use App\Models\Dao\RuleConfigurationsDao;
use App\Models\Dao\RuleCriteriaDao;
use App\Models\Dao\RuleDao;
use App\Models\Dao\UserInfoDao;
use App\Models\Entity\Rule;
use App\Utils\Config;
use App\Utils\ServerTime;
use Swoft\Bean\Annotation\Bean;


/**
 * @Bean()
 */
class HotUpdateService
{
    /**
     * 检查一个用户是否符合一组规则
     * 一组规则中最多有两个规则一个父规则一个子规则，一组规则只能匹配一组数据
     *
     * @param array $rules ,[[],[]]
     * @param array $globalInfo ,全局信息
     * @return mixed
     */
    public function matchGroupRules($rules)
    {
        $isMatch = true;
        $contain = [];
        $matchId = "";
        $now = time();
        foreach ($rules as $rule) {

            if ($rule['scheduleStart'] != 0 || $rule['scheduleEnd'] != 0) {
                if ($now < $rule['scheduleStart']) {
                    $isMatch &= false;
                    break;
                }
                if ($rule['scheduleEnd'] != 0 && $now > $rule['scheduleEnd']) {
                    $isMatch &= false;
                    break;
                }
            }
            $isMatch &= $this->matchSingleRule($rule);
        }
        if ($isMatch) { // 匹配完成
            foreach ($rules as $rule) {
                if ($rule['isParent'] == RuleDao::ISPARENT_NO) { // 只有叶子节点才有匹配的数据
                    $contain = RuleConfigurationsDao::getValueByRuleId($rule['ruleId']);
                    $matchId = $rule['ruleId'];
                }
            }
        }
        return [
            "contain" => $contain,
            "matchId" => $matchId
        ];
    }

    /**
     * 检查一个用户是否符合一个规则
     *
     * @param array $rule ,一条规则
     * @param array $globalInfo ,全局信息
     */
    private function matchSingleRule($rule)
    {
        $attrs = RuleCriteriaDao::getCriteriasByRuleId($rule['ruleId']);
        $isMatch = true;

        if (!$attrs) {
            return false;
        }

        foreach ($attrs as $attr) {
            if (!in_array($attr['choices'], RuleCriteriaDao::$allChoices)) {
                return false;
            }
            $value = json_decode($attr['value'], true);
            if (json_last_error()) {
                return false;
            }
            switch ($attr['aid']) {
                case RuleAttributeDao::AID_BUILDID: // buildId:uuid
                    $isMatch &= $this->compareBuildId($value, \Yii::$app->globalInfo->uid, $attr['choices']);
                    break;
                case RuleAttributeDao::AID_APPVERSION: // app version
                    $isMatch &= $this->compareAppVersion($value, substr(commonVar()->getAppVersion(), 0, 5), $attr['choices']);
                    break;
                case RuleAttributeDao::AID_OPERATIONSYSTEM: // os
                    $isMatch &= $this->compareOs($value, commonVar()->getPlatform(), $attr['choices']);
                    break;
                case RuleAttributeDao::AID_CUSTOMERID_LAST_NUMBER_LIMIT: // customerId 尾数过滤条件
                    $isMatch &= $this->compareCustomerId($value, commonVar()->getUid(), $attr['choices']);
                    break;
            }
            if (!$isMatch) {
                return false;
            }
        }
        return true;
    }

    /**
     * os的比较 单个
     */
    private function compareOs($value, $os, $choice)
    {
        foreach ($value as $v) {
            if ($choice === "=") {
                return ($v === $os);
            }
        }
        return false;
    }

    /**
     * AppVersion的比较 多个
     */
    private function compareAppVersion($versions, $appVersion, $choice)
    {
        return $this->versionCompare($versions, $appVersion, $choice);
    }

    /**
     * os version 的比较
     */
    private function compareOsVersion($versions, $osVersion, $choice)
    {
        return $this->versionCompare($versions, $osVersion, $choice);
    }

    /**
     * buildId的比较 多个
     */
    private function compareBuildId($buildIds, $uuid, $choice)
    {
        return $this->multiItemIncluded($buildIds, $uuid, $choice);
    }

    /**
     * 国家或地区的比较
     */
    private function compareGeoLan($value, $country, $choice)
    {
        return $this->multiItemIncluded($value, $country, $choice);
    }

    /**
     * customerId尾数选择
     */
    private function compareCustomerId($value, $customerId, $choice)
    {
        /**
         * 取最后一位
         */
        $num = substr($customerId, -1);
        return $this->numRangeCompare($value[0], $num, $choice);
    }

    /**
     * 多个选项的包括与不包括的比较
     *
     * @param array $items
     * @param mixed $item
     * @param string $choice
     * @return bool
     */
    private function multiItemIncluded($items, $item, $choice)
    {
        if ($choice === "=") {
            return in_array($item, $items);
        } elseif ($choice === "!=") {
            return !in_array($item, $items);
        }
        return false;
    }

    /**
     * 版本号的比较
     *
     * @param array $desVersions
     * @param string $selfVersion
     * @param string $choice
     * @return bool
     */
    private function versionCompare($desVersions, $selfVersion, $choice)
    {
        $isMatch = ($choice == '!=');
        foreach ($desVersions as $ver) {
            $result = version_compare($selfVersion, $ver);
            if ($choice === "=") {
                $isMatch |= ($result == 0);
            } elseif ($choice === "<") {
                $isMatch |= ($result < 0);
            } elseif ($choice === ">") {
                $isMatch |= ($result > 0);
            } elseif ($choice === "!=") {
                $isMatch &= ($result != 0);
            } elseif ($choice === ">=") {
                $isMatch |= ($result >= 0);
            } elseif ($choice === "<=") {
                $isMatch |= ($result <= 0);
            }
        }
        return $isMatch;
    }

    /**
     * 数字范围的比较
     */
    private function numRangeCompare($limit, $num, $choice)
    {
        $isMatch = ($choice == '!=');
        $result = (int)$num - (int)$limit;
        if ($choice === "=") {
            $isMatch |= ($result == 0);
        } elseif ($choice === "<") {
            $isMatch |= ($result < 0);
        } elseif ($choice === ">") {
            $isMatch |= ($result > 0);
        } elseif ($choice === "!=") {
            $isMatch &= ($result != 0);
        } elseif ($choice === ">=") {
            $isMatch |= ($result >= 0);
        } elseif ($choice === "<=") {
            $isMatch |= ($result <= 0);
        }
        return $isMatch;
    }


}