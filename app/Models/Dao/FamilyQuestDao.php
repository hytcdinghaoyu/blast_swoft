<?php
namespace App\Models\Dao;
use App\Constants\QuestDraw;
use App\Constants\QuestState;
use App\Constants\RedisKey;
use App\Models\Entity\FamilyQuest;
use App\Models\Entity\UserInfo;
use App\Utils\Config;
use App\Utils\ServerTime;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;

/**
 *
 * @Bean()
 */
class FamilyQuestDao
{
    const KEY_CHAPTER_REWARD = 'gardenUnlockReward';

    public static function getInfo($uid)
    {
        $quests =  Utils::formatArrayValue(Query::table(FamilyQuest::class)->condition(['uid' => $uid])->get()->getResult());

        if (empty($quests)) {
            return [];
        }
        return $quests;
    }

    public static function updateRecord($record)
    {

        $record['updated_at'] = ServerTime::getTestTime();
        $find = FamilyQuest::findOne(['uid' => $record['uid'], 'questId' => $record['questId']])->getResult();
        if (empty($find)){
            $record['created_at'] = ServerTime::getTestTime();
            $find = new FamilyQuest();
            $find->fill($record);
            $find->save()->getResult();
            self::afterSave($record);
        }else{
            $oldFields = $find->toArray();
            $find->fill($record);
            $find->update()->getResult();
            self::afterUpdate($oldFields,$record);
        }

        return true;
    }

    public static function setChapterReward($uuid, $chapterId)
    {
        $redis = bean('redis');
        return $redis->hSet(sprintf(RedisKey::CONSTRUCTION, $uuid), $chapterId, 1);
    }

    public static function getChapterReward($uuid, $chapterId)
    {
        $redis = bean('redis');
        return $redis->hGet(sprintf(RedisKey::CONSTRUCTION, $uuid), $chapterId);
    }

    public static function batchInsertQuest($fields, $insertData)
    {
        foreach ($insertData as $key=>$value){
            $insertData[$key]['updated_at'] = ServerTime::getTestTime();
            $insertData[$key]['created_at'] = ServerTime::getTestTime();
        }
        Query::table(FamilyQuest::class)->batchInsert($insertData)->getResult();


    }

    public static function afterSave($fields)
    {
        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());

        if (isset($fields[0])) {//多条数据

        }else{//单条数据
            $tmp = $fields;
            $fields = [$tmp];
        }
        foreach ($fields as $key=>$field){
            if ($field['state'] == QuestState::COMPLETE){
                $userInfo = self::useStar($userInfo,$field['questId']);
                $userInfo = self::addPopulation($userInfo,$field['questId']);
                $userInfo = self::addProsperity($userInfo,$field['questId']);
            }elseif ($field['state'] != QuestState::INIT){
                $userInfo = self::useStar($userInfo,$field['questId']);
            }
            if($field['draw'] == QuestDraw::TRIGGERED){
                $userInfo = self::addProsperityFromDraw($userInfo,$field['questId']);
            }

        }

        $userInfo->update()->getResult();


    }



    public static function afterUpdate($fieldOld,$fieldNew)
    {
        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());
        if (isset($fieldOld['state']) && $fieldOld['state'] == QuestState::INIT && $fieldNew['state'] != QuestState::INIT) {
            $userInfo = self::useStar($userInfo,$fieldNew['questId']);
        }
        if (isset($fieldOld['state']) && $fieldOld['state'] != QuestState::COMPLETE && $fieldNew['state'] == QuestState::COMPLETE) {
            $userInfo = self::addPopulation($userInfo,$fieldNew['questId']);
            $userInfo = self::addProsperity($userInfo,$fieldNew['questId']);
        }
        if (isset($fieldOld['draw']) && $fieldOld['draw'] == QuestDraw::NOT_TRIGGERED && $fieldNew['draw'] == QuestDraw::TRIGGERED) {
            $userInfo = self::addProsperityFromDraw($userInfo,$fieldNew['questId']);
        }
        $userInfo->update()->getResult();
    }

    public static function useStar(UserInfo $userInfo,$questId)
    {
        $usedStar = Config::loadJson('gardenQuest')[$questId]['star']??0;
        $finalStar = max(0, $userInfo->getRemainStars()-$usedStar);
        $userInfo->setRemainStars($finalStar);
        return $userInfo;

    }

    public static function addPopulation(UserInfo $userInfo,$questId)
    {
        $addedPopulation = Config::loadJson('gardenQuest')[$questId]['population']??0;
        $finalPopulation = $userInfo->getPopulation()+$addedPopulation;
        $userInfo->setPopulation($finalPopulation);
        return $userInfo;


    }

    public static function addProsperity(UserInfo $userInfo,$questId)
    {
        $addedProsperity = (Config::loadJson('gardenQuest')[$questId]['population']??0) * 10;
        $finalProsperity = $userInfo->getProsperity()+$addedProsperity;
        $userInfo->setProsperity($finalProsperity);
        return $userInfo;

    }

    public static function addProsperityFromDraw(UserInfo $userInfo,$questId)
    {
        $addedProsperity = Config::loadJson('gardenQuest')[$questId]['exp']??0;
        $finalProsperity = $userInfo->getProsperity()+$addedProsperity;
        $userInfo->setProsperity($finalProsperity);
        return $userInfo;

    }


}