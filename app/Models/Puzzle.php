<?php
namespace App\Models;


use App\Base\BaseModel;
use App\Constants\ActivityType;
use App\Constants\FlowActionConst;
use App\Constants\PieceId;
use App\Constants\RedisKey;
use App\datalog\AccountFlow;
use App\Models\Dao\CenterActivityDao;
use App\Utils\ServerTime;

/**
 * Class Puzzle
 * 拼图
 */
class Puzzle extends BaseModel
{

    const DAILY_SEND_LIMIT = 5;//每日赠送次数上限
    const DAILY_REQUEST_LIMIT = 3;//每日请求次数上限
    const DAILY_RECEIVE_LIMIT = 50;//每日请求次数上限

    protected $expire = 3456000;//过期时间，大于赛季时间

    protected $config = [];

    protected $redis = [];

    private $key = '';

    function __construct(array $config)
    {
        $this->config            = $config;
        $this->config ['season'] = isset ($config ['season'])
            ? $config ['season'] : $this->getCurrentSeason();
        $this->key               = $this->getKey();

        $this->redis = bean('redis');
    }



    public function getAllPieces(){
        $pieces = $this->redis->hGetAll($this->key);
        $ret = [];
        if($pieces){
            foreach ($pieces as $key => $value){
                $ret[$key] = intval($value);
            }
            $this->redis->expire($this->key,$this->expire);
        }
        return $ret;
    }

    public function addOnePiece($piece_id, $num)
    {
        $this->redis->hIncrby($this->key,$piece_id,$num);
        AccountFlow::newFlow ( $piece_id, $num,  FlowActionConst::DETAIL_PUZZLE_SCENE_UNKNOWN, '');
        return $this->redis->expire($this->key,$this->expire);
    }

    public function decOnePiece($piece_id, $num)
    {
        $this->redis->hIncrby($this->key,$piece_id, -$num);
        AccountFlow::newFlow ( $piece_id, $num,  FlowActionConst::DETAIL_PUZZLE_SCENE_SEND, '');
    }

    /**
     * 消耗碎片
     * @return bool
     */
    public function convertPiece()
    {
        $piece_total = $this->redis->hGetAll($this->key);
        foreach ($piece_total as $key => &$piece_num) {
            $piece_num = $piece_num - 1;
            AccountFlow::newFlow ( $key, -1, FlowActionConst::DETAIL_PUZZLE_SCENE_CONSUME);
        }
        $this->redis->hMSet($this->key,$piece_total);
        return $this->redis->expire($this->key,$this->expire);
    }

    /**
     * 检查是否集齐了所有碎片
     * @return bool
     */
    public function checkPiece(){
        $piece_total = PieceId::getConstantsValues();
        $pieces = $this->redis->hGetAll($this->key);

        $diff = array_diff($piece_total,array_keys($pieces));
        if(!empty($diff)){
            return false;
        };

        foreach ($pieces as $piece) {
            if($piece <= 0){
                return false;
            }
        }

        return true;
    }

    public function checkOnePiece($pieceId){
        $piece = $this->redis->hGet($this->key, $pieceId);

        return $piece ? true : false;
    }


    public static function transferOnePiece($fromUid, $toUid, $pieceId){
        $fromPuzzle = new self(['uid' => $fromUid]);
        $fromPuzzle->decOnePiece($$pieceId, 1);

        $toPuzzle = new self(['uid' => $toUid]);
        $toPuzzle->addOnePiece($pieceId, 1);

        return true;
    }


    private function getKey()
    {
        return sprintf(RedisKey::PUZZLE_KEY, $this->config['uid'], $this->config['season']);
    }

    /**
     * 自增赛季合成拼图的次数
     * @return mixed
     */
    public function incComposeTimes(){
        $times = $this->redis->incrBy($this->getComposeTimesKey(),1);
        $this->redis->expire($this->getComposeTimesKey(),$this->expire);
        return $times;
    }

    public function getComposeTimes(){
        return $this->redis->get($this->getComposeTimesKey()) ? $this->redis->get($this->getComposeTimesKey()) : 0;
    }

    /**
     * 赛季合成次数
     * @return string
     */
    private function getComposeTimesKey()
    {
        return sprintf(RedisKey::PUZZLE_COMPOSE_TIMES, $this->config['uid'], $this->config['season']);
    }

    /**
     * 每日赠送同一好友次数
     * @param $fUid
     * @return mixed
     */
    public function incSendTimes($fUid){
        $times = $this->redis->hIncrBy($this->getSendTimesKey(),$fUid,1);
        return $times;
    }
    public function getSendTimes($fUid){
        $times =  $this->redis->hGet($this->getSendTimesKey(), $fUid);
        return $times ? $times : 0;
    }
    private function getSendTimesKey()
    {
        return sprintf(RedisKey::PUZZLE_SEND_TIMES, $this->config['uid'], date('Ymd'));
    }



    /**
     * 每日请求同一好友次数
     * @param $fUid
     * @return mixed
     */
    public function incRequestTimes($fUid){
        $times = $this->redis->hIncrBy($this->getRequestTimesKey(),$fUid,1);
        return $times;
    }
    public function getRequestTimes($fUid){
        $times =  $this->redis->hGet($this->getRequestTimesKey(), $fUid);
        return $times ? $times : 0;
    }
    private function getRequestTimesKey()
    {
        return sprintf(RedisKey::PUZZLE_REQUEST_TIMES, $this->config['uid'], date('Ymd'));
    }

    /**
     * 每日接受赠送次数
     * @param $fUid
     * @return mixed
     */
    public function incReceiveTimes(){
        $times = $this->redis->IncrBy($this->getReceiveTimesKey(),1);
        return $times;
    }
    public function getReceiveTimes(){
        $times =  $this->redis->get($this->getReceiveTimesKey());
        return $times ? $times : 0;
    }
    private function getReceiveTimesKey()
    {
        return sprintf(RedisKey::PUZZLE_RECEIVE_TIMES, $this->config['uid'], date('Ymd'));
    }



    /**
     * 获取当前赛季信息
     * @return mixed|string
     */
    public function getCurrentSeason()
    {
        $now = ServerTime::getTestTime();
        $seasons = CenterActivityDao::getSeasonListByType(ActivityType::PUZZLE);
        $current_season = [];

        if($seasons){
            //在赛季中, 取当前赛季
            foreach ($seasons as $season){
                if($season['start_at'] < $now && $season['end_at'] > $now){
                    $current_season = $season;
                }
            }
        }

        return $current_season ? $current_season['name'] : '';
    }


}
