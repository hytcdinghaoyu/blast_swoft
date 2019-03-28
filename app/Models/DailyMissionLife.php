<?php

namespace App\Models;


use App\Constants\RedisKey;

class DailyMissionLife
{
    const DAILYMISSION_NUM = 2;

    public $key;
    public $redis;

    public function __construct()
    {
        $today = strtotime(date('Y-m-d', time()));
        $globalInfo = globalInfo();
        $this->key = sprintf(RedisKey::VOUCHER, $today, $globalInfo->getUuid());
        $this->redis = bean('redis');
        if ($this->redis === false) {
            throw new \RedisException();
        }
    }

    public function get($field)
    {
        return $this->redis->hGet($this->key, $field);
    }

    public function getAll()
    {
        return $this->redis->hGetAll($this->key);
    }

    public function create($numberArr)
    {
        $this->redis->hMSet($this->key, $numberArr);
        $this->redis->expire($this->key, 172800);
    }

    public static function getNumber()
    {
        $life = new self();
        $numbers = $life->getAll();

        if (empty($numbers)) {
            $num = static::DAILYMISSION_NUM;
        } else {
            $sign = 0;
            if (isset($numbers['sign'])) {
                $sign = $numbers['sign'];
            }
            $num = static::DAILYMISSION_NUM + ( int )$numbers['purchase'] + ( int )$numbers['give'] + $sign - ( int )$numbers['used'];
            if ($num < 0) {
                $num = 0;
            }
        }

        return $num;
    }

    public static function getGiveNumber()
    {
        $life = new self();
        $giveNumber = $life->redis->hGet($life->key, 'give');
        if ($giveNumber === false) {
            $giveNumber = 0;
        }

        return $giveNumber;
    }

    public static function incrBy($type, $number = 1)
    {
        $typeArr = ['used', 'purchase', 'give', 'sign'];
        if (!in_array($type, $typeArr)) {
            return false;
        }

        $life = new self();
        $numbers = $life->getAll();

        if (empty($numbers)) {
            $numberArr = ['used' => 0, 'purchase' => 0, 'give' => 0, 'sign' => 0];
            $numberArr = array_merge($numberArr, [$type => $number]);
            $life->create($numberArr);
            $num = $number;
        } else {
            $num = $life->redis->hIncrBy($life->key, $type, $number);
        }

        return $num;
    }
}