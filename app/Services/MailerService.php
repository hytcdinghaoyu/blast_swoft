<?php
namespace App\Services;

use App\Constants\FlowActionConst;
use App\Constants\Message;
use App\Constants\MoneyType;
use App\Models\MailMsg;
use App\Constants\MailMsgType;
use App\Models\Puzzle;
use Swoft\Redis\Redis;;
use yii\helpers\Json;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;


/**
 * @Bean
 * @package App\Services
 */
class MailerService{
    
    const TIMEOUT = 604800;
    
    const GM_ID = 0;

    const MAX_MAIL_NUM = 100;

    const ID_OFFSET = 100000;
    /**
     * @Inject
     * @var Redis
     */
    private $redis;
    
    public static function keyPrefix()
    {
        return "sys_mailer";
    }
    
    public function findAllByUid($uid){
        $pks = $this->fetchMsgIdsByUid($uid);
        
        if(!$pks){
            return [];
        }
        
        return $this->findAllByMsgIds($pks);
    }
    
    /**
     * 查找过去一段时间用户消息id
     * @param $uid
     * @return array
     */
    public function fetchMsgIdsByUid($uid)
    {
        $poolKey = $this->idPoolKey($uid);
        $pks = $this->redis->zRangeByScore($poolKey, time() - static::TIMEOUT, time());
        if (!$pks) {
            return [];
        }
        $this->redis->zRemRangeByScore($poolKey, 0, time() - static::TIMEOUT - 1000);
        
        return $pks;
        
    }
    
    public function findAllByMsgIds($primaryKeyArr){
        $return = [];
        foreach ($primaryKeyArr as $id) {
            $k = $this->getIncIndex($id);
            $r = $this->redis->hGetAll($k);
            if($r){
                $r['reward'] = $r['reward'] ? Json::decode($r['reward']) : [];
                $r['content'] = $r['content'] ? Json::decode($r['content']) : [];
                $return[] = $r;
            }
        }
        return $return;
    }

    public function setNormalMailStatus($msgIdArr, $isWatched, $isReceived, $isDeleted = 0)
    {//只有等于1才设置
        if (empty($msgIdArr)) {
            return;
        }
        $globalInfo = globalInfo();
        foreach ($msgIdArr as $msgId) {
            $msg = $this->findOneById($msgId);
            if (empty($msg)) {
                continue;
            }
            $k = $this->getIncIndex($msgId);
            if ($isWatched) {
                $this->redis->HSET($k, 'isWatched', $isWatched);
            }
            if ($isReceived) {
                $curReceived = $msg['isReceived'];//防重发
                $reward = $msg['reward'];
                if (!empty($reward) && $curReceived != 1) {//防重发
                    bean(PropertyService::class)->handleBatch($reward, FlowActionConst::MAILER_REWARD,$msg['type']);
                }
                $this->redis->HSET($k, 'isReceived', $isReceived);
            }
            if ($isDeleted) {
                $this->delete($globalInfo->getUid(), $msgId);
            }

        }
    }

    public function findOneById($id){
        $k = $this->getIncIndex($id);
        $ret = $this->redis->hGetAll($k);
        $ret['reward'] = $ret['reward'] ? Json::decode($ret['reward']) : [];
        $ret['content'] = $ret['content'] ? Json::decode($ret['content']) : [];
        return $ret ? $ret : [];
    }
    
    public function send(MailMsg $msg)
    {
        $id = $this->incId() + static::ID_OFFSET;
        $msg->setMessageId($id);
        $newKey = $this->getIncIndex($msg->id);
        
        //TODO:validate msg
//        if(!$this->validate()){
//            return $this->getErrors();
//        }
        
        //TODO:讲道理这里要用事务，但是腾讯云不支持
        $this->redis->hMset($newKey, $msg->getAttributes());
        $this->redis->EXPIRE($newKey, static::TIMEOUT);
        $this->redis->ZADD($this->idPoolKey($msg->receiverUid), time(), $msg->id);
        
        return true;
    }
    
    public function handleReward($msg){

        //请求体力需要回复一条消息
        if($msg['type'] == MailMsgType::REQUEST_LIVES){//请求生命

            $friendLives = bean(FriendLivesService::class)->initMine();
            $friendLives->incrSendNum([$msg['senderUid']]);
            $msg['remain_send_num'] = $friendLives->getSendRemainNum();
            $mailMsg = new MailMsg();
            $mailMsg->setSenderUid($msg['receiverUid'])->setReceiverUid($msg['senderUid']);
            $mailMsg->setType(MailMsgType::SEND_LIVES)->setReward([MoneyType::LIVE => 1]);
            $this->send($mailMsg);

        }elseif($msg['type'] == MailMsgType::REQUEST_PIECE){//请求碎片
            //自己的碎片数量减一,不足则提示
            $puzzleModel = new Puzzle(['uid' => $msg['receiverUid']]);
            $pieceIdArr = array_keys($msg['reward']);
            if(!$puzzleModel->checkOnePiece($pieceIdArr[0])){
                return [
                    'errCode' => Message::PIECE_NUMBER_NOT_ENOUGH
                ];
            }
            $puzzleModel->decOnePiece($pieceIdArr[0], 1);
            $mailMsg = new MailMsg();
            $mailMsg->setSenderUid($msg['receiverUid'])->setReceiverUid($msg['senderUid']);
            $mailMsg->setType(MailMsgType::SEND_PIECE)->setReward($msg['reward']);
            $this->send($mailMsg);
            unset($msg['reward']);
        }elseif($msg['type'] == MailMsgType::SEND_PIECE){
            $puzzleModel = new Puzzle(['uid' => $msg['receiverUid']]);
            $dailyReceiveTime = $puzzleModel->getReceiveTimes();
            if($dailyReceiveTime >= Puzzle::DAILY_RECEIVE_LIMIT){
                return [
                    'errCode' => Message::PIECE_RECEIVE_NUM_LIMITED
                ];
            }
            $puzzleModel->incReceiveTimes();
        }
        
        if(isset($msg['reward']) && $msg['reward']){
            $reward = $msg['reward'];
            bean(PropertyService::class)->handleBatch($reward, FlowActionConst::MAILER_REWARD, $msg['type']);
        }
        
        return $msg;
    }
    
    public function delete($uid, $id){
        
        if(!$this->isValidMsgId($uid, $id)){
            return false;
        }
        
        $this->redis->ZREM($this->idPoolKey($uid), $id);
        $this->redis->delete($this->getIncIndex($id));
        return true;
    }
    
    public function isValidMsgId($uid, $id){
        if(!$this->redis->ZSCORE($this->idPoolKey($uid), $id)){
            return false;
        }
        return true;
    }
    
    public function idPoolKey($uid){
        return self::keyPrefix() . ':pool:' . $uid;
    }
    
    public function incId(){
        return $this->redis->INCR(self::keyPrefix() . ':inc');
    }
    
    public function getIncIndex($id){
        return self::keyPrefix() . ':index:' . $id;
    }
    
    
    
}