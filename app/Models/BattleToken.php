<?php

namespace App\Models;


use App\Base\RedisHashModel;

class BattleToken extends RedisHashModel
{
    public $uid;

    public $battleType;

    public $secret;

    public $expire = 604800;


    public function rules()
    {
        return [
            [['uid', 'battleType', 'secret'], 'required'],
        ];
    }

    public static function primaryFields(){
        return [
            'uid',
            'battleType'
        ];
    }


}