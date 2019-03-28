<?php
namespace App\Models\Dao;


use App\Models\Entity\League;
use Swoft\Bean\Annotation\Bean;
/**
 *
 * @Bean()
 */
class LeagueDao
{
    public static function createNewRecord($uid,$rank,$upDown,$created_at,$updated_at)
    {
        $obj = new League();
        $obj->setUid($uid);
        $obj->setRank($rank);
        $obj->setUpDown($upDown);
        $obj->setCreatedAt($updated_at);
        $obj->setUpdatedAt($updated_at);
        $obj->save()->getResult();
    }

    /**
     * 另外的初始化方法
     */
    public static function updateRecord($obj,$rank,$upDown,$updated_at)
    {
        if (isset($rank)){
            $obj->setRank($rank);
        }
        if (isset($upDown)){
            $obj->setUpDown($upDown);
        }
        if (isset($updated_at)){
            $obj->setUpdatedAt($updated_at);
        }
        $obj->update()->getResult();
    }

    public static function checkLeagueInfo($uid)
    {
        $info = League::findOne(['uid' => $uid])->getResult();
        if (empty($info)){
            return true;
        }elseif($info->getCreatedAt() == $info->getUpdatedAt()){
            return true;
        }
        return false;
    }


}
