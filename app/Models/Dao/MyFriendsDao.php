<?php
namespace App\Models\Dao;

use App\Models\Entity\MyFriends;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use yii\helpers\ArrayHelper;

/**
 *
 * @Bean()
 */
class MyFriendsDao
{
    public static function findAllFriendsInfo($uid){
        $ret = [];
        $rows =  Query::table(MyFriends::class)->condition(['uid' => $uid])->get()->getResult();

        if(empty($rows)){
            return $ret;
        }

        $uid_arr = ArrayHelper::getColumn($rows, 'fuid');
        $ret =  bean(UserInfoDao::class)->getAllByUidArr($uid_arr);

        return $ret;
    }

    public static function findAllFriendsUid($uid){
        $ret = [];

        $rows =  Query::table(MyFriends::class)->condition(['uid' => $uid])->get()->getResult();
        if(empty($rows)){
            return $ret;
        }

        $uid_arr = ArrayHelper::getColumn($rows, 'fuid');

        return $uid_arr;
    }
}