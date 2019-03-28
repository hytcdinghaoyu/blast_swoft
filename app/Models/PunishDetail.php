<?php
namespace App\Models;
use App\Base\RedisHashModel;

class PunishDetail extends RedisHashModel
{
    public $openId;

    public $type;

    public $reason;

    public $start_at;

    public $end_at;

    public $expire;


    public function rules()
    {
        return [
            [['openId', 'type', 'reason','start_at','end_at','expire'], 'required'],
        ];
    }

    public static function primaryFields(){
        return [
            'openId',
            'type'
        ];
    }


}