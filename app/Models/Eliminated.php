<?php

namespace App\Models;


use App\Constants\RedisKey;
use App\Utils\Utils;

class Eliminated
{
    /**
     * 记录用户当日累计的元素
     * @param int $uid  用户uid
     * @param array $eliminated 积累的元素id与个数列表
     */
    public static function updateNumberByType($uid, $eliminated)
    {
        $itemIds = array_keys($eliminated);
        //拼接rediskey(key中拼接当天日期)
        $keyEliminated = static::getKeyByType("eliminated", $uid);
        //查询redis中是否存在该key
        // 目前使用redisMaster,redis用途：消除元素数量，缓存时间一天
        $redis = bean('redis');
        /**
         * redis不可用时，直接返回，保证score/newLevelUpdate，丢失部分消除元素信息
         */
        if ($redis === false) {
            return;
        }
        $eliminatedString = $redis->get($keyEliminated);
        //如果存在
        if ($eliminatedString !== false) {
            //取出该值
            $eliminatedArr = json_decode($eliminatedString, true);
            foreach ($itemIds as $id) {
                if (array_key_exists($id, $eliminatedArr)) {
                    $eliminatedArr[$id] += $eliminated[$id];
                } else {
                    $eliminatedArr[$id] = $eliminated[$id];
                }
            }
            $redis->setEx($keyEliminated, 86400, json_encode($eliminatedArr));
        } else {//如果不存在
            $redis->setEx($keyEliminated, 86400, json_encode($eliminated));
        }
    }

    public static function getKeyByType($type, $uid)
    {
        $today = date("Ymd");
        switch ($type) {
            case "eliminated" :
                return sprintf(RedisKey::ELIMINATE_MESSAGES, $uid, $today);
                break;
        }
    }
}