<?php

namespace App\Models;


use App\Base\RedisHashModel;

class IdipPunishInfo extends RedisHashModel
{
    public $openId;

    public $banUser = 0;//封号

    public $banRankingLeague = 0;//禁止

    public $banRankingDailyMission = 0;

    public $banRankingProsperity = 0;

    public $zeroIncome = 0;


    public function rules()
    {
        return [
            [['openId', 'banUser', 'banRankingLeague', 'banRankingDailyMission','banRankingProsperity','zeroIncome'], 'required'],
        ];
    }

    public static function primaryFields(){
        return [
            'openId'
        ];
    }


}
