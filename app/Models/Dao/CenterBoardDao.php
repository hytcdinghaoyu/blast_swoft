<?php

namespace App\Models\Dao;

use App\Models\Entity\CenterBoard;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use Swoftx\Aop\Cacheable\Annotation\Cacheable;
/**
 *
 * @Bean()
 */
class CenterBoardDao
{
    const STATUS_ACTIVE = 1;

    const STATUS_CLOSE = 0;

    const CACHE_TIME = 600;

    public static $allCacheKey = "CenterBoard";

    /**
     * @Cacheable(key="CenterBoard", ttl=600)
     */
    public function findNewOne($language = 'zh')
    {
        $time = ServerTime::getTestTime();
        $res = Utils::formatArrayValue(Query::table(CenterBoard::class)->condition(['is_actived' => self::STATUS_ACTIVE, 'language' => $language,
            ['start_at', '<', $time], ['end_at', '>', $time]])->orderBy('created_at','DESC')->one(['title_config', 'content'])->getResult());
        return $res;

    }

    public function findAllBoard()
    {
        return Utils::formatArrayValue(Query::table(CenterBoard::class)->get()->getResult());
    }
}