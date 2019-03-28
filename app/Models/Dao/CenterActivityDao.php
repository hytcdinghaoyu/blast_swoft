<?php

namespace App\Models\Dao;

use App\Constants\ActivityType;
use App\Models\Entity\CenterActivity;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;

/**
 * @Bean()
 */
class CenterActivityDao
{
    const NOT_BEEN_DELETED = 0;
    const HAS_BEEN_DELETED = 1;

    /**
     * 获取当前赛季
     */
    public static function getCurrentSeasonByType(int $type)
    {
        $severTime = ServerTime::getTestTime();
        return Utils::formatArrayValue(Query::table(CenterActivity::class)->condition(['type' => $type, 'is_deleted' => self::NOT_BEEN_DELETED, ['end_at', '>', $severTime], ['start_at', '<', $severTime]])->one()->getResult());
    }

    /**
     * 获取所有赛季
     */
    public static function getSeasonListByType(int $type, $cacheTime = 0)
    {

        if ($cacheTime > 0) {
            if ($type == ActivityType::LEAGUE){
                $key = static::getCacheKeyByType('all');
                $cache = bean('cache');
                if ($cache->has($key)) {
                    $seasons = $cache->get($key);
                    $seasons = json_decode($seasons, true);
                    return $seasons;
                }
                $seasons = Query::table(CenterActivity::class)->condition(['is_deleted' => self::NOT_BEEN_DELETED, 'type' => $type])->get()->getResult();
                $cache->set($key, json_encode($seasons), $cacheTime);//测试阶段300改成1
                return $seasons;
            }

        }
        return Utils::formatArrayValue(Query::table(CenterActivity::class)->condition(['is_deleted' => self::NOT_BEEN_DELETED, 'type' => $type])->get()->getResult());

    }


    public static function getCacheKeyByType($type)
    {
        if (in_array($type, ['current', 'next', 'pre', 'all'], true)) {
            return "league" . $type . "season";
        }
        return false;
    }


}