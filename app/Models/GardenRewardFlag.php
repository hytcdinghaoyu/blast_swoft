<?php
namespace App\Models;

use App\Base\RedisHashModel;

class GardenRewardFlag extends RedisHashModel
{
    public $uid;

    public $rewardId;

    public function rules()
    {
        return [
            [['uid', 'rewardId'], 'required'],
        ];
    }


    public static function primaryFields(){
        return [
            'uid',
            'rewardId'
        ];
    }


}