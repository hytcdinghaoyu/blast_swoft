<?php
namespace App\Models\Dao;

use App\Models\Entity\FamilyBirds;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;


/**
 *
 * @Bean()
 */
class FamilyBirdsDao
{
    public static function getInfo($uid)
    {
        $birds =  Utils::formatArrayValue(Query::table(FamilyBirds::class)->condition(['uid' => $uid])->get()->getResult());
        if (empty($birds)) {
            return [];
        }
        return $birds;
    }

    public static function updateRecord($record)
    {
        $record['updated_at'] = ServerTime::getTestTime();
        $find = FamilyBirds::findOne(['uid' => $record['uid'], 'birdId' => $record['birdId']])->getResult();
        if (empty($find)){
            $record['created_at'] = ServerTime::getTestTime();
            $find = new FamilyBirds();
            $find->fill($record);
            $find->save()->getResult();
        }else{
            $find->fill($record);
            $find->update()->getResult();
        }

        return true;
    }

    public static function batchInsertBirds($fields, $insertData)
    {
        foreach ($insertData as $key=>$value){
            $insertData[$key]['updated_at'] = ServerTime::getTestTime();
            $insertData[$key]['created_at'] = ServerTime::getTestTime();
        }
        Query::table(FamilyBirds::class)->batchInsert($insertData)->getResult();
    }
}