<?php
namespace App\Models;

use App\Base\BaseModel;
use App\Constants\MailMsgType;

class MailMsg extends BaseModel{
    
    public $id;
    
    public $senderUid;

    public $senderName;
    
    public $receiverUid;
    
    public $type;
    
    public $content = '';
    
    public $reward = '';

    public $sendTime = '';//发送的时间

    public $isWatched = 0;//是否被查看

    public $isReceived = 0;//是否被领取
    
    public function rules()
    {
        return [
            [['id', 'senderUid', 'receiverUid', 'type'], 'required'],
            [['content', 'reward'], 'string'],
            ['type', 'in', 'range' => MailMsgType::getConstantsValues()]
        ];
    }
    
    public function setMessageId($id){
        $this->id = $id;
        return $this;
    }
    
    public function setSenderUid($uid){
        $this->senderUid = $uid;
        return $this;
    }
    
    public function setReceiverUid($uid){
        $this->receiverUid = $uid;
        return $this;
    }
    
    public function setType($type){
        $this->type = $type;
        return $this;
    }
    
    public function setContent($content){
        $this->content = $content;
        return $this;
    }
   
    public function setReward($reward = []){
        foreach ($reward as &$r){
            $r = (int)$r;
        }
        $this->reward = json_encode($reward);
        return $this;
    }

    public function setSendTime($time){
        $this->sendTime = $time;
        return $this;
    }

    public function setSenderName($name){
        $this->senderName = $name;
        return $this;
    }


}