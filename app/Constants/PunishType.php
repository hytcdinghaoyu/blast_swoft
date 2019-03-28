<?php

namespace App\Constants;

final class PunishType extends BasicEnum
{

    CONST BAN_USER = 1;//禁止登录

    CONST BAN_RANKING_LEAGUE = 2;//禁止联赛排行榜

    CONST BAN_RANKING_DAILY_MISSION = 3;//禁止周赛排行榜

    CONST BAN_RANKING_PROSPERITY = 4;//禁止繁荣度排行榜

    CONST ZERO_INCOME = 5;//零收益

    public static function checkRankingType($rankingType){
        if (in_array($rankingType,[2,3,4])){
            return true;
        }
        return false;
    }






}