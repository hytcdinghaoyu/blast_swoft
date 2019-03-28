<?php

namespace App\Utils;

//use Yii;
use App\Constants\RedisKey;
use App\Models\Dao\CenterUserTestDao;



class ServerTime
{

    /**
     * 获取设置的时间偏移量
     *
     * @return int
     */
    public static function getOffset()
    {

        $test_customers = CenterUserTestDao::getAllCustomerIds();
        //$test_customers = [];
        $globalInfo = globalInfo();
        if (!in_array($globalInfo->getUid(), $test_customers)) {
            return 0;
        } else {
            $redis = bean('redis');
            return $redis->get(sprintf(RedisKey::SIGNUP_OFFSET, $globalInfo->getUid()));
        }


    }

    /**
     * 获取当前测试时间
     *
     * @return mixed
     */
    public static function getTestTime()
    {
        $offset = static::getOffset();
        if (isset($offset)) {
            return time() + $offset;
        } else {
            return time();
        }
    }
}