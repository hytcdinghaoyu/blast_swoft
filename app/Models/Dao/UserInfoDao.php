<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Models\Dao;

use App\Constants\RedisKey;
use App\Constants\ThirdPlatform;
use App\Models\Entity\UserInfo;
use App\Services\PayServiceInterface;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Db;
use Swoft\Db\Query;

/**
 *
 * @Bean()
 */
class UserInfoDao
{
    public $max_lives = 25;
    public $recover_rate = 300;//每隔5分钟回复一点

    CONST STATUS_ONLINE = 1;
    CONST STATUS_OFFLINE = 0;
    CONST STATUS_BANNED_TMP = 2;
    CONST STATUS_BANNED_FOREVER = 3;

    public function getAllByUidArr($uid_arr)
    {
        return Query::table(UserInfo::class)->condition(['uid' => $uid_arr])->get()->getResult();
    }

    public function updateFields($uid, array $fields)
    {
        $userInfoArr = Query::table(UserInfo::class)->condition(['uid' => $uid])->one()->getResult();
        foreach ($fields as $field => $num) {
            if ($field == 'silver_coin' || $field == 'gold_coin') {
                if ($userInfoArr[$field] + $num < 0) {
                    $fields[$field] = -$userInfoArr[$field];
                }
            }
            if ($field == 'lives') {
                if ($userInfoArr[$field] + $num < 0) {
                    $fields[$field] = -$userInfoArr[$field];
                }
                if ($userInfoArr[$field] + $num > $this->max_lives) {
                    $fields[$field] = $this->max_lives - $userInfoArr[$field];
                }
            }
        }

        $sql = "UPDATE `user_info` SET";
        foreach ($fields as $field => $num) {
            $sql .= " `$field` = `$field` + $num,";
        }
        $sql = rtrim($sql, ',');
        $sql .= " where `uid` = $uid";
        return Db::query($sql, [], 'api')->getResult();

    }

    public function updateAll($uid, array $fields){
        return UserInfo::updateAll($fields,['uid'=>$uid])->getResult();
    }

    public function findOneByUid($uid)
    {
        $userInfo = UserInfo::findById($uid)->getResult();
        if (!empty($userInfo)){
            if ($this->max_lives !== $userInfo->getLives()) {
                $newLives = floor((time() - $userInfo->getLastLoginAt()) / ($this->recover_rate));
                if ($newLives != 0) {
                    $livesNow = $userInfo->getLives() + $newLives;
                    $userInfo->setLives(min($this->max_lives, $livesNow));
                    $userInfo->setLastLoginAt(time());
                    $userInfo->update()->getResult();
                }
            }
        }
        return $userInfo;
    }

    public function findOneByUidQuery($uid)
    {
        return Utils::formatArrayValue(Query::table(UserInfo::class)->condition(['uid' => $uid])->one()->getResult());
    }


    public function getUsernameByUid($uid)
    {
        $res = Query::table(UserInfo::class)->condition(['uid' => $uid])->one()->getResult();
        return isset($res['username']) ? $res['username'] : '';
    }


    /**
     * 设置每日活跃用户
     */
    public function setDailyActive()
    {

        $extraSign = '';

        $redis = bean('redis');
        $globalInfo = globalInfo();
        $dailyKey = sprintf(RedisKey::DAILY_ACTIVE_ISSET, date('Ymd'), $globalInfo->getUid());
        if ($redis->get($dailyKey)) {
            return;
        }
        $userInfo = $this->findOneByUid($globalInfo->getUid());
        //首次登陆放入redis
        $lastLoginDay = date('Ymd', $userInfo->getLastLoginAt());
        if ($lastLoginDay != date('Ymd')) {
            $redis->SADD(sprintf(RedisKey::FIRST_LOGIN_HOUR, ThirdPlatform::valueToLowerStr($globalInfo->getThirdBind()),
                date('YmdH')), $globalInfo->getUid() . ':' . $globalInfo->getChannel() . ':' . $extraSign);

            $redis->setEx($dailyKey, 86400, 1);
        }
    }

    public static function activePlatformVip()
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');
        $redisKey = sprintf(RedisKey::PLATFORM_START_VIP, date('Ymd', ServerTime::getTestTime()), $globalInfo->getUid());
        return $redis->SETEX($redisKey, 86401, 1);
    }


    public static function isPlatformVip()
    {
        $globalInfo = globalInfo();
        $redis = bean('redis');
        $redisKey = sprintf(RedisKey::PLATFORM_START_VIP, date('Ymd', ServerTime::getTestTime()), $globalInfo->getUid());
        return $redis->GET($redisKey) ? true : false;
    }


    /**
     * 初始化user_info
     * @param     $uid
     * @param int $gold
     * @return UserInfo|bool
     */
    public function createNewUser($uid, $gold = 200)
    {
        $info = new UserInfo();
        $info->setUid($uid);
        $info->setSilverCoin(400);

        //调用tencent接口赠送游戏币
        $payService = bean(PayServiceInterface::class);
        $payService->presentMoney($gold);

        $info->setLives($this->max_lives);
        $info->setLast(time());
        $info->setNewLevel(1);
        $info->setStars(0);
        $info->setUsername(Utils::randomName());
        $info->setUpdatedAt(time());
        $info->setCreatedAt(time());
        if (!$info->save()->getResult()) {
            return false;
        }
        return $info;
    }

    public function updateUserInfo(UserInfo $userInfo)
    {
        $userInfo->update()->getResult();
    }

    public function findOneByUidAndSelect($select, $uid)
    {
        return Utils::formatArrayValue(Query::table(UserInfo::class)->condition(['uid' => $uid])->one($select)->getResult());
    }

    public static function setStatusOut($uid, $status)
    {
        UserInfo::updateAll(['status' => $status], ['uid' => $uid])->getResult();
    }

    public static function setStatusOnline($uid)
    {
        UserInfo::updateAll(['status' => self::STATUS_ONLINE], ['uid' => $uid])->getResult();
    }


}
