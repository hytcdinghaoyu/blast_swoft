<?php

namespace App\Models\Dao;

use App\Constants\BulletInBoardConst;
use App\Constants\RedisKey;
use App\Models\Entity\CenterBulletinBoard;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use Swoftx\Aop\Cacheable\Annotation\Cacheable;
/**
 *
 * @Bean()
 */
class CenterBulletinBoardDao
{
    public static $allCacheKey = "BulletinBoard";


    public function getAll()
    {
        $all = Utils::formatArrayValue(Query::table(CenterBulletinBoard::class)->condition(['is_actived' => 1, 'is_deleted' => 0])->get()->getResult());
        if (empty($all)) {
            return [];
        }
        $result ['main'] = $all;
        foreach ($all as $kItem) {
            $result ['pair'] [$kItem['id']] = bean(CenterBulletinLanguageDao::class)->getAllByBulletinId($kItem['id']);
        }
        return $result;
    }

    public function getById($id)
    {
        $all = static::getAllFromCache();
        foreach ($all['main'] as $kBulletin) {
            if ($kBulletin['id'] == $id) {
                return $kBulletin;
            }
        }
        return [];
    }

    /**
     * 过滤公告板
     * @param unknown $bulletins
     */
    public function bulletinFilter($bulletins)
    {
        $testCustomers = CenterUserTestDao::getAllCustomerIds();
        $globalInfo = globalInfo();
        $now = ServerTime::getTestTime();
        $appVersion = commonVar()->getAppVersion();
        $appVersion = $appVersion == 1 ? $appVersion : str_replace('.', '', substr($appVersion, 0, 5));
        $redis = bean('redis');
        $redisKey = RedisKey::APPOINT;
        if (!is_array($bulletins)){
            return [];
        }
        foreach ($bulletins as $kKey => $vBulletin) {
            /**
             *判断公告版的有效时间
             */
            if ($now < $vBulletin['start_at'] || ($vBulletin['end_at'] != 0 && $now > $vBulletin['end_at'])) {
                unset($bulletins[$kKey]);
                continue;
            }
            /**
             *判断公告版的 版本限制
             */
            $versions = json_decode($vBulletin['version'], true);
            if (!empty($versions) && !in_array($appVersion, $versions)) {
                unset($bulletins[$kKey]);
                continue;
            }
            /**
             *判断公告板是不是测试的
             */
            if ($vBulletin['is_testing'] && !in_array($globalInfo->getUid(), $testCustomers)) {
                unset($bulletins[$kKey]);
                continue;
            }
            /**
             * 预约活动 activity_id = 2，判断是否预约
             */
            if ($vBulletin['activity_id'] == 2) {
                if (!$redis->sIsMember($redisKey, commonVar()->getOpenId())) {
                    unset($bulletins[$kKey]);
                }
                continue;
            }

        }
        return array_values($bulletins);
    }

    /**
     * @Cacheable(key="BulletinBoard", ttl=300)
     */
    private function getAllFromCache()
    {
        $all = $this->getAll();
        return $all;
    }

    public function findAllGlobalMail(){
        $time = ServerTime::getTestTime();
        return Utils::formatArrayValue(Query::table(CenterBulletinBoard::class)->condition(['type' => BulletInBoardConst::GlobalMail,
            'is_actived' => 1, 'is_deleted' => 0,'is_testing' => 0,
            ['start_at', '<', $time], ['end_at', '>', $time]])->orderBy('end_at','DESC')
            ->get(['id', 'reward_id', 'start_at', 'end_at'])->getResult());
    }
}