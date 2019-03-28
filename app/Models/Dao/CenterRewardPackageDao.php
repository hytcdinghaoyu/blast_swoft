<?php

namespace App\Models\Dao;

use App\Models\Entity\CenterRewardPackage;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use Swoftx\Aop\Cacheable\Annotation\Cacheable;
/**
 *
 * @Bean()
 */
class CenterRewardPackageDao
{
    public static $allCacheKey = "RewardPackage";

    public function getAll()
    {
        $all = Utils::formatArrayValue(Query::table(CenterRewardPackage::class)->condition(['is_deleted' => 0])->get()->getResult());
        return $all;
    }

    public function getById($id)
    {
        $all = $this->getAllFromCache();
        foreach ($all as $kReward) {
            if ($kReward['id'] == $id) {
                return $kReward;
            }
        }
        return [];
    }

    /**
     * @Cacheable(key="RewardPackage", ttl=300)
     */
    private function getAllFromCache()
    {
        $all = $this->getAll();
        return $all;
    }
    public function findAllInRewardId($rewardIdArr)
    {
        $all = Query::table(CenterRewardPackage::class)->condition(['id' => $rewardIdArr])->get()->getResult();
        return $all;
    }
    public function findOneByRewardId($rewardId)
    {
        $all = Query::table(CenterRewardPackage::class)->condition(['id' => $rewardId])->one()->getResult();
        return $all;
    }
}