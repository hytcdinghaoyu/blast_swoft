<?php

namespace App\Models\Dao;


use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Db\Db;
use Swoft\Db\Query;
use Swoft\Bean\Annotation\Bean;
use App\Models\Entity\Score;


/**
 *
 * @Bean()
 */
class ScoreDao
{

    public static function findAllByUid($uid)
    {
        $allScores = Utils::formatArrayValue(Query::table(Score::class)->condition(['uid' =>$uid])->get(['level', 'score', 'star'])->getResult());
        return $allScores;
    }

    public static function findOneByCondition($condition)
    {
        $record = Score::findOne($condition)->getResult();
        return $record;
    }

    public static function findByLevel($condition)
    {
        $record = Utils::formatArrayValue(Query::table(Score::class)->condition($condition)->one()->getResult());
        return $record;
    }

    public static function batchInsertScores($fields, $insertData)
    {
        foreach ($insertData as $value)
        {
            foreach ($value as $v){
                if (!is_numeric($v)){
                    return 0;
                }
            }
        }
        $str = implode(",", $fields);

        $sql = "INSERT INTO score($str) VALUES ";
        foreach ($insertData as $key => $value) {
            if ($key == 0) {
                $strTmp = "(" . implode(",", $value) . ")";
                $sql .= $strTmp;
            } else {
                $strTmp = ",(" . implode(",", $value) . ")";
                $sql .= $strTmp;
            }

        }
        $sql .= ' ON DUPLICATE KEY UPDATE score = VALUES(score), star = VALUES(star);';
        return $result = Db::query($sql, [], 'api')->getResult();
    }
    public static function createOneRecord($uid,$level,$score,$star){
        $time = ServerTime::getTestTime();
        $scoreObj = new Score();
        $scoreObj->setUid($uid);
        $scoreObj->setLevel($level);
        $scoreObj->setStar($star);
        $scoreObj->setScore($score);
        $scoreObj->setUpdatedAt($time);
        $scoreObj->setCreatedAt($time);
        $scoreObj->save()->getResult();

    }
}