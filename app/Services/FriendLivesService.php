<?php

namespace App\Services;

use App\Utils\ServerTime;
use App\Constants\RedisKey;
use Swoft\Bean\Annotation\Bean;


/**
 * @Bean()
 */
class FriendLivesService
{
    public $uid;
    public $uuid;

    public $max_request_num = 200000;
    public $max_send_num = 200000;

    public $request_num = null;
    public $send_num = null;


    private $request_list = null;
    private $send_list = null;
    private $redis;
    private $key_request;
    private $key_send;

    public function initMine()
    {

        $this->redis = bean('redis');
        $globalInfo =globalInfo();
        $this->uid = $globalInfo->getUid();
        $this->key_request = sprintf(RedisKey::GIVE_LIFE, date('Ymd', time()), $this->uid);
        $this->key_send = sprintf(RedisKey::SEND_OUT, date('Ymd', time()), $this->uid);
        return $this;

    }

    public function getRequestList()
    {
        if ($this->request_list === null) {
            $this->request_list = $this->redis->HKEYS(sprintf(RedisKey::GIVE_LIFE, date('Ymd', time()), $this->uid));
        }
        return $this->request_list;
    }


    public function getRequestRemainNum(): int
    {
        $this->request_num = count($this->getRequestList());
        $remain_request_num = $this->max_request_num - $this->request_num;
        return ($remain_request_num >= 0) ? $remain_request_num : 0;
    }

    public function getSendList()
    {
        if ($this->send_list === null) {
            $this->send_list = $this->redis->HKEYS(sprintf(RedisKey::SEND_OUT, date('Ymd', time()), $this->uid));
        }
        return $this->send_list;
    }

    public function getSendRemainNum(): int
    {
        $this->send_num = count($this->getSendList());
        $remain_send_num = $this->max_send_num - $this->send_num;
        return ($remain_send_num >= 0) ? $remain_send_num : 0;
    }

    public function incrRequestNum(array $fuids)
    {
        $requested_list = $this->redis->HKEYS(sprintf(RedisKey::GIVE_LIFE, date('Ymd', time()), $this->uid));
        $to_request = array_slice(array_diff($fuids, $requested_list), 0,
            $this->max_request_num - count($requested_list));
        $after_request = array_merge($requested_list, $to_request);

        if (!empty($to_request)) {
            $result = $this->redis->HMSET(sprintf(RedisKey::GIVE_LIFE, date('Ymd', time()), $this->uid), array_fill_keys($to_request, 1));
            if ($result && empty($requested_list)) {
                $this->redis->expire($this->key_request, 86400);
            }
        }

        $this->request_num = count($after_request);
        return [$to_request, $after_request];
    }

    public function incrSendNum(array $fuids)
    {
        $sended_list = $this->redis->HKEYS(sprintf(RedisKey::SEND_OUT, date('Ymd', time()), $this->uid));
        $to_send = array_slice(array_diff($fuids, $sended_list), 0, $this->max_send_num - count($sended_list));
        $after_send = array_merge($sended_list, $to_send);

        if (!empty($to_send)) {
            $send = $this->redis->HMSET(sprintf(RedisKey::SEND_OUT, date('Ymd', time()), $this->uid), array_fill_keys($to_send, 1));
            if ($send && empty($sended_list)) {
                $this->redis->expire(sprintf(RedisKey::SEND_OUT, date('Ymd', time()), $this->uid), 86400);
            }
        }

        $this->send_num = count($after_send);
        return [$to_send, $after_send];
    }
}