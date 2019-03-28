<?php
namespace App\Models\Dao;

use App\Models\Entity\CenterUserTest;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use yii\helpers\ArrayHelper;

/**
 *
 * @Bean()
 */
class CenterUserTestDao
{
    const HAS_DELETED = 1;
    const HAS_NOT_DELETED = 0;

    public static function getAllCustomerIds()
    {
        $cache = bean('cache');
        $cids = $cache->get('cids');
        if (!$cids) {//空的返回false
            $all =  Utils::formatArrayValue(Query::table(CenterUserTest::class)->condition(['deleted' => self::HAS_NOT_DELETED, ['created_at','>', strtotime('-1 month')]])->get()->getResult());
            $cids = ArrayHelper::getColumn($all, 'uid');
            $cache->set('cids', json_encode($cids), 3 * 60);
        }else{
            $cids = json_decode($cids,true);
        }

        return $cids;


    }
}