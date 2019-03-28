<?php
namespace App\Models;



use App\Constants\RedisKey;
use App\Utils\Config;
use App\Utils\ServerTime;
use yii\helpers\ArrayHelper;

class Shop
{
    public static function filterActiveItem(&$itemList, $item, $activityName = '')
    {
        $globalInfo = globalInfo();
        $now = ServerTime::getTestTime();
        /**
         * 第一个if是为了兼容老版本代码
         */
        if ($now > 1511769600 && $now < 1512979200) {
            $itemSpecialList = [
                "xs_seasonal" => ["package" => ["1281" => 3, "1285" => 3, "1543" => 1, "gold" => 150], "money" => 2.99],
                "s_seasonal" => [
                    "package" => [
                        "1281" => 6,
                        "1283" => 3,
                        "1282" => 3,
                        "1285" => 3,
                        "1284" => 3,
                        "1547" => 1
                    ],
                    "money" => 4.99
                ],
                "m_seasonal" => [
                    "package" => [
                        "1281" => 6,
                        "1283" => 3,
                        "1282" => 3,
                        "1285" => 3,
                        "1284" => 3,
                        "1547" => 1
                    ],
                    "money" => 9.99
                ],
                "xl_seasonal" => [
                    "package" => [
                        "1281" => 9,
                        "1283" => 9,
                        "1282" => 9,
                        "1285" => 9,
                        "1284" => 6,
                        "1547" => 1
                    ],
                    "money" => 29.99
                ],
                "xxxl_seasonal" => [
                    "package" => [
                        "1281" => 15,
                        "1283" => 15,
                        "1282" => 15,
                        "1285" => 15,
                        "1284" => 18,
                        "1547" => 1
                    ],
                    "money" => 59.99
                ],
                "l_seasonal" => ["package" => ["1281" => 6, "1282" => 6, "1544" => 1, "gold" => 540], "money" => 19.99],
                "xxl_seasonal" => [
                    "package" => [
                        "1281" => 6,
                        "1283" => 12,
                        "1285" => 12,
                        "1284" => 6,
                        "1544" => 1,
                        "1279" => 880
                    ],
                    "money" => 39.99
                ]
            ];
            if (isset($itemSpecialList[$item])) {
                $itemList = $itemSpecialList;
                static::setTag(2, $globalInfo->getUuid());
            }
        } else {
            /**
             * 必须指定活动名
             */
            if (!empty($activityName)) {
                /**
                 * 新版本使用以下代码
                 */
                $activityList = Config::loadJson('shopActivity');
                if (isset($activityList[$activityName])) {
                    $itemList = $activityList[$activityName];
                    static::setTag($activityName, $globalInfo->getUuid());
                }
            }

        }
    }

    public static function getActiveShopEnable()
    {
        $now = ServerTime::getTestTime();
        $globalInfo = globalInfo();
        /**
         * 前两个if是为了兼容老版本的客户端代码
         */
        if ($now > 1511510400 && $now < 1511596800) {
            $enable = static::getTag(1, $globalInfo->getUuid());
            $endtime = 1511596800 - $now;
            return [
                'enable' => !$enable,
                'endTime' => $endtime
            ];
        } elseif ($now > 1511769600 && $now < 1511856000) {
            $enable = static::getTag(2, $globalInfo->getUuid());
            $endtime = 1511856000 - $now;
            return [
                'enable' => !$enable,
                'endTime' => $endtime
            ];
        } else {
            /**
             * 新版本代码
             */
            $activityList = Config::loadJson('shopActivity');
            $activityNames = array_keys($activityList);
            $activityTimes = [];
            foreach ($activityNames as $activityName) {
                $startTime = strtotime(str_replace('.', '-', $activityName));
                $activityTimes[$activityName] = [
                    'start' => $startTime + 3600 * 8,   //活动开始时间默认为当天UTC时间8点钟开始
                    'end' => $startTime + 3600 * 8 + 86400 //活动时间默认持续一天
                ];
            }
            $activityNow = self::getCurrent($now, $activityTimes);
            if (empty($activityNow)) {
                /**
                 * 如果当前没有活动，看看后面还有没有活动
                 */
                $activityNext = self::getNext($now, $activityTimes);
                if (!empty($activityNext)) {
                    /**
                     * 如果后面有活动
                     */
                    $name = $activityNext;
                    $startTime = $activityTimes[$activityNext]['start'] - $now;
                    $endTime = $activityTimes[$activityNext]['end'] - $now;
                } else {
                    /**
                     * 如果后面也没有活动了
                     */
                    $name = '';
                    $startTime = -1;
                    $endTime = -1;
                }
            } else {
                $tag = self::getTag($activityNow, $globalInfo->getUuid());
                /**
                 * 如果当前有活动
                 */
                $name = $activityNow;
                $startTime = 0;
                $endTime = !$tag ? $activityTimes[$activityNow]['end'] - $now : 0;
            }
            /**
             * 返回结果
             */
            return [
                'name' => $name,
                'startTime' => $startTime,
                'endTime' => $endTime
            ];
        }
    }

    public static function setTag($tag, $uuid)
    {
        $key = static::getTagKey($tag, $uuid);
        $redis = static::getTagRedis($key);
        $redis->setEx($key, 3600 * 7, 1);
    }

    public static function getTag($tag, $uuid)
    {
        $key = static::getTagKey($tag, $uuid);
        $redis = static::getTagRedis($key);
        return $redis->get($key);
    }

    /**
     * 找到当前活动的信息
     */
    protected static function getCurrent($nowTime, $activities)
    {
        foreach ($activities as $name => $activity) {
            if ($activity['start'] < $nowTime && $activity['end'] > $nowTime) {
                return $name;
            }
        }
    }

    /**
     * 找到下一个活动的信息
     */
    protected static function getNext($nowTime, $activities)
    {
        ArrayHelper::multisort($activities, 'start', SORT_ASC);
        foreach ($activities as $name => $activity) {
            if ($activity['start'] > $nowTime) {
                return $name;
            }
        }
    }

    private static function getTagRedis($key)
    {
        return bean('redis');
    }

    private static function getTagKey($tag, $uuid)
    {
        return sprintf(RedisKey::GET_NEXT_INFO, $tag, $uuid);
    }
}