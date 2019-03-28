<?php

namespace App\Models;


use App\Constants\RedisKey;
use App\Utils\Utils;

class EliminatedDaily
{
    const DAILYMISSION_NUM = 2;

    public $uid;
    public $key;
    public $redis;

    public function __construct()
    {
        $globalInfo = globalInfo();
        $this->uid = $globalInfo->getUid();
        $this->redis = bean('redis');
        if ($this->redis === false) {
            throw new \RedisException();
        }
    }

    public function getKey($itemId)
    {
        $weekOfYear = date('Y-m-d', Utils::monday());
        return sprintf(RedisKey::ELIMINATE_PIG, $weekOfYear, $this->uid, $itemId);
    }

    public static function incrItem($itemId, $number)
    {
        $dayOfWeek = date('N', time());
        $eliminated = new self();
        $result = $eliminated->redis->hIncrBy($eliminated->getKey($itemId), $dayOfWeek, $number);
        $eliminated->redis->expire($eliminated->getKey($itemId), 691200);// 8天
        return $result;
    }

    public static function getNumberOfToday($itemId)
    {
        $dayOfWeek = date('N', time());
        $eliminated = new self();
        $number = (int)$eliminated->redis->hGet($eliminated->getKey($itemId), $dayOfWeek);
        return $number ? $number : 0;
    }

    public static function getNumberOfWeek($itemId)
    {
        $eliminated = new self();
        $all = $eliminated->redis->hGetAll($eliminated->getKey($itemId));
        $totalNumber = array_sum($all);
        return $totalNumber;
    }
}