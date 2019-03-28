<?php

namespace App\Controllers\Cloud;

use App\Constants\LeaderBoardType;
use App\Constants\Message;
use App\Controllers\CommonController;
use App\Utils\Utils;
use Swoft\Bean\Annotation\Inject;
use App\Services\LeaderBoardService;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;


/**
 * 排行榜服务.
 *
 * @Controller(prefix="/leaderboard")
 */
class LeaderboardController extends CommonController
{
    
    
    /**
     * @Inject
     * @var LeaderBoardService
     */
    private $leaderBoardService;
    /**
     * 排行榜分数累加提交
     * @RequestMapping(route="submit", method={RequestMethod::POST})
     * @param string  $leaderBoard 排行榜名
     * @param integer $score       需要累加的分数
     *
     * @return array
     */
    public function actionSubmit($leaderBoard, $score)
    {
        if (!LeaderBoardType::isValidValue($leaderBoard)) {
            return [
                "code" => Message::LEADERBOARD_NOT_FOUND
            ];
        }
        
        $commonVar = commonVar();
        $result = $this->leaderBoardService->updateByType($commonVar->getUid(), $leaderBoard, $score);
        if ($result === false) {
            return [
                "code" => Message::LEADERBOARD_REDIS_DISABLED
            ];
        }
        return [
            "code" => 1
        ];
    }
    
    /**
     * 排行榜指定用户的排名与信息
     * @RequestMapping(route="get", method={RequestMethod::POST})
     *
     * @param string $leaderBoard 排行榜名
     * @param array  $uid_arr
     *
     * @return array
     */
    public function actionGet(string $leaderBoard, array $uid_arr)
    {
        if (!LeaderBoardType::isValidValue($leaderBoard)) {
            return [
                "code" => Message::LEADERBOARD_NOT_FOUND
            ];
        }
        $commonVar = commonVar();
        $uid_arr[] = $commonVar->getUid();
        $rankInfo = $this->leaderBoardService->getCustomerRankInfoByLeaderBoard($leaderBoard, $uid_arr);
        if ($rankInfo === false) {
            return [
                "code" => Message::LEADERBOARD_REDIS_DISABLED
            ];
        }
        //数据格式规范
        foreach ($rankInfo as $key=>$value){
            $rankInfo[$key]['accountId'] = (string)$rankInfo[$key]['accountId'];
        }
        return [
            "code"     => 1,
            "rankInfo" => $rankInfo
        ];
    }
    
    /**
     * 排行榜信息批量查询
     * @RequestMapping(route="list", method={RequestMethod::POST})
     *
     * @param string  $leaderBoard 排行榜名
     * @param integer $amount      查询的人数
     * @param integer $offset      开始查询的位置
     *
     * @return array
     */
    public function actionList(string $leaderBoard, int $amount, int $offset)
    {
        if (!LeaderBoardType::isValidValue($leaderBoard)) {
            return [
                "code" => Message::LEADERBOARD_NOT_FOUND
            ];
        }
        
        $rankInfo = $this->leaderBoardService->getListByAmount($leaderBoard, $amount, $offset);
        if ($rankInfo === false) {
            return [
                "code" => Message::LEADERBOARD_REDIS_DISABLED
            ];
        }
        //数据格式规范
        foreach ($rankInfo as $key=>$value){
            $rankInfo[$key]['accountId'] = (string)$rankInfo[$key]['accountId'];
        }
        return [
            "code"     => 1,
            "rankInfo" => $rankInfo
        ];
    }
    
    
}