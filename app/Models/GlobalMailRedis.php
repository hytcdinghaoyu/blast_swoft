<?php

namespace App\Models;

use App\Base\RedisHashModel;

class GlobalMailRedis extends RedisHashModel
{
    public $uid;

    public $boardId;//邮件id

    public $type = null;

    public $senderName = '';//发送人姓名

    public $sendTime = '';//发送时间

    public $isDeleted = 0;//邮件是否被删除

    public $isWatched = 0;//邮件是否被查看

    public $isReceived = 0;//邮件中的奖励是否被领取，无奖励默认为0

    public $reward = '';

    public $content = '';

    public $expire = 2592000;//默认一个月失效




    public function rules()
    {
        return [
            [['uid','boardId','senderName','sendTime', 'isDeleted', 'isWatched', 'isReceived','expire','type','reward','content'], 'required'],
        ];
    }

    public static function primaryFields(){
        return [
            'uid',
            'boardId'
        ];
    }


}
