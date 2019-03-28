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
class RuleAttributeDao
{
// uuid
    const AID_BUILDID = 1;
    // app版本
    const AID_APPVERSION = 2;
    // 操作系统
    const AID_OPERATIONSYSTEM = 3;
    // 操作系统版本号
    const AID_OPERATIONSYSTEMVERSION = 4;
    // 地区和语言
    const AID_GEOGRAPHY_LANGUAGE = 5;
    // 用户选择（根据customerid 尾数）
    const AID_CUSTOMERID_LAST_NUMBER_LIMIT = 6;

    // 数字
    const TYPE_NUMERICE = 1;
    // 日期
    const TYPE_DATE = 2;
    // 字符串
    const TYPE_STRING = 3;
    // 单选
    const TYPE_RADIO = 4;
    // 多选
    const TYPE_MULTISELECT = 5;

    public static function getAtrType($aid)
    {
        $map = static::atrTypeMap();
        return $map[$aid];
    }

    private static function atrTypeMap()
    {
        return [
            static::AID_BUILDID => static::TYPE_MULTISELECT,
            static::AID_APPVERSION => static::TYPE_STRING,
            static::AID_OPERATIONSYSTEM => static::TYPE_RADIO,
            static::AID_OPERATIONSYSTEMVERSION => static::TYPE_STRING,
            static::AID_GEOGRAPHY_LANGUAGE => static::TYPE_MULTISELECT,
            static::AID_CUSTOMERID_LAST_NUMBER_LIMIT => static::TYPE_RADIO
        ];
    }


}