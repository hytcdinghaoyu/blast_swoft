<?php

namespace App\Controllers\Api;


use App\Constants\ActivityType;
use App\Constants\ItemID;
use App\Constants\MailMsgType;
use App\Constants\Message;
use App\Constants\PieceId;
use App\Controllers\CommonController;
use App\Models\Dao\CenterActivityDao;
use App\Models\MailMsg;
use App\Models\Puzzle;
use App\Services\MailerService;
use App\Services\PropertyService;
use App\Utils\ServerTime;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use App\Constants\FlowActionConst;
use App\Utils\Config;


/**
 * 用户模块.
 * @Controller(prefix="/puzzle")
 */
class PuzzleController extends CommonController
{

    /**
     * @RequestMapping(route="rewardpiece")
     * @return array
     */
    public function actionRewardPiece($pieceArr = [])
    {
        if(!$pieceArr || !is_array($pieceArr)){
            return [
                'code' => 37001,
                'msg'  => 'param error'
            ];
        }

        $globalInfo = globalInfo();
        $uid       = $globalInfo->getUid();

        $puzzleModel = new Puzzle ([
            'uid' => $uid,
        ]);
        foreach ($pieceArr as $piece_id => $num) {
            if(!PieceId::isValidValue($piece_id)){
                return $this->returnError(Message::PIECE_ID_INVALID);
            }
            if(!is_numeric($num)){
                return $this->returnError(Message::PIECE_NUMBER_TYPE_ERROR);
            }
            $puzzleModel->addOnePiece($piece_id, $num);
        }

        return [
            'code' => 1,
        ];
    }


    /**
     * @RequestMapping(route="consume")
     * @return array
     */
    public function actionConsume()
    {
        $globalInfo = globalInfo();
        $uid       = $globalInfo->getUid();

        $puzzleModel = new Puzzle(['uid' => $uid]);

        if (!$puzzleModel->checkPiece()) {
            return $this->returnError(Message::MUST_HAVE_ALL_PIECES);
        }

        if ($puzzleModel->convertPiece()) {
            $rewardConfig = Config::loadJson ( 'puzzleReward' );
            //根据合成次数，发放首次和后续奖励
            $composeTimes = $puzzleModel->getComposeTimes();
            $reward = $rewardConfig[min($composeTimes, count($rewardConfig)-1)];
            //判断奖励类型，有可能是皮肤，皮肤通过城建接口发放
            $rewardFlag = true;
            foreach ($reward as $itemId=>$value){
                if(is_array($value)){//奖励为皮肤不发放
                    $rewardFlag = false;
                    break;
                }

            }
            if ($rewardFlag){
                bean(PropertyService::class)->handleBatch($reward, FlowActionConst::ACTION_PUZZLE_REWARD, date ( 'Y.m.d' ));
            }
            $composeTimes = $puzzleModel->incComposeTimes();
        } else {
            return $this->returnError(Message::PIECE_NUMBER_NOT_ENOUGH);
        }

        return [
            'code' => 1,
            'composeTimes' => isset($composeTimes) ? $composeTimes : 0,
            'reward' => $reward,
        ];

    }


    /**
     * @RequestMapping(route="info")
     * @return array
     */
    public function actionInfo(){
        $globalInfo = globalInfo();
        $uid       = $globalInfo->getUid();

        $puzzleModel = new Puzzle(['uid' => $uid]);
        //碎片信息和消息
        $pieces = $puzzleModel->getAllPieces();

        //赛季合成拼图次数
        $composeTimes = $puzzleModel->getComposeTimes();
        //赛季信息
        $currentSeason = CenterActivityDao::getCurrentSeasonByType(ActivityType::PUZZLE);
        $seasonEndTime = isset($currentSeason) ? $currentSeason['end_at'] - ServerTime::getTestTime() : 0;

        return [
            'code' => 1,
            'pieces' => $pieces ? $pieces : [],
            'composeTimes' => $composeTimes ? intval($composeTimes) : 0,
            'seasonName' => isset($currentSeason['name']) ? $currentSeason['name'] : '',
            'seasonEndTime' => $seasonEndTime
        ];

    }

    /**
     * 索要拼图碎片
     * @RequestMapping(route="request")
     * @return array
     */
    public function actionRequest(int $friendUid, int $pieceId)
    {

        if(!PieceId::isValidValue($pieceId)){
            return $this->returnError(Message::PIECE_ID_INVALID);
        }
        $globalInfo = globalInfo();
        //每天向同一玩家索要的拼图上限为3块
        $puzzleModel = new Puzzle(['uid' => $globalInfo->getUid()]);
        $dailyRequestTimes = $puzzleModel->getRequestTimes($friendUid);
        if($dailyRequestTimes >= Puzzle::DAILY_REQUEST_LIMIT){
            return $this->returnError(Message::PIECE_REQUEST_NUM_LIMITED);
        }

        $puzzleModel->incRequestTimes($friendUid);
        $msg = new MailMsg();
        $msg->setSenderUid($globalInfo->getUid());
        $msg->setReceiverUid($friendUid);
        $msg->setType(MailMsgType::REQUEST_PIECE);
        $msg->setReward([$pieceId => 1]);
        bean(MailerService::class)->send($msg);

        return $this->returnSuccess();
    }


    /**
     * 赠送拼图碎片
     * @RequestMapping(route="send")
     * @return array
     */
    public function actionSend(int $friendUid, int $pieceId)
    {

        if(!PieceId::isValidValue($pieceId)){
            return $this->returnError(Message::PIECE_ID_INVALID);
        }

        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();

        //每天向同一玩家赠送的拼图上限为5块
        $puzzleModel = new Puzzle(['uid' => $uid]);
        $dailySendTimes = $puzzleModel->getSendTimes($friendUid);
        if($dailySendTimes >= Puzzle::DAILY_SEND_LIMIT){
            return $this->returnError(Message::PIECE_SEND_NUM_LIMITED);
        }

        //检查玩家是否拥有此碎片
        if(!$puzzleModel->checkOnePiece($pieceId)){
            return $this->returnError(Message::PIECE_NUMBER_NOT_ENOUGH);
        }

        $puzzleModel->decOnePiece($pieceId, 1);
        $puzzleModel->incSendTimes($friendUid);
        $msg = new MailMsg();
        $msg->setSenderUid($uid);
        $msg->setReceiverUid($friendUid);
        $msg->setType(MailMsgType::SEND_PIECE);
        $msg->setReward([$pieceId => 1]);
        bean(MailerService::class)->send($msg);


        return $this->returnSuccess();
    }

}

