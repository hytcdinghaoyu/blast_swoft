<?php
namespace App\Services;

use App\Constants\FlowActionConst;
use App\Models\Dao\CenterBulletinBoardDao;
use App\Models\Dao\CenterBulletinLanguageDao;
use App\Models\Dao\CenterRewardPackageDao;
use App\Models\GlobalMailRedis;
use App\Constants\MailMsgType;
use App\Utils\ServerTime;
use yii\helpers\ArrayHelper;
use Swoft\Bean\Annotation\Bean;



/**
 * Class SysMailer
 * @Bean
 * @package App\Services
 */
class GlobalMailService{
    public $cacheKey = "global_mail";
    public $cacheTime = 180;


    const GET_ALL_DATA = 1;

    const GET_ID_ARR = 2;


    public function getAll($flag)// 1返回所有全服邮件详细信息 ,2返回过滤后的全服邮件idArr
    {

        $cache = bean("cache");
        $result = $cache->get($this->cacheKey);
        if (empty($result)) {
            $all = bean(CenterBulletinBoardDao::class)->findAllGlobalMail();
            if (empty($all)) {
                return [];
            }

            $boardIdArr = ArrayHelper::getColumn($all, 'id');
            $rewardIdArr = ArrayHelper::getColumn($all, 'reward_id');
            //从CenterBulletinLanguage 中取出标题与内容
            $tmp = bean(CenterBulletinLanguageDao::class)->getAllByBulletinId($boardIdArr);
            $titleConfigArr = ArrayHelper::map($tmp, 'bulletin_board_id', 'title_config');//json
            $contentArr = ArrayHelper::map($tmp, 'bulletin_board_id', 'content');
            //从CenterRewardPackage 中取奖励
            $tmp = bean(CenterRewardPackageDao::class)->findAllInRewardId($rewardIdArr);
            $rewardArr = ArrayHelper::map($tmp, 'id', 'contain_list');//json
            foreach ($all as $key => $board) {
                $all[$key]['reward'] = $rewardArr[$board['reward_id']];
                $all[$key]['title_config'] = json_decode($titleConfigArr[$board['id']], true);
                $all[$key]['content'] = $contentArr[$board['id']];
                $all[$key]['sendTime'] = $board['start_at'];
                $all[$key]['senderName'] = "村长";
                $all[$key]['senderUid'] = '0';
            }
            $result = $all;
            $cache->set($this->cacheKey, json_encode($all), $this->cacheTime);
        }else{
            $result = json_decode($result,true);
        }

        if ($flag == static::GET_ALL_DATA) {
            return $result;
        } elseif ($flag == static::GET_ID_ARR) {
            $filterResult = $this->filterGlobalMail($result);
            return $filterResult;
        }

    }

    public function filterGlobalMail($result)
    {
        $globalInfo = globalInfo();
        foreach ($result as $key => $board) {
            if ($obj = GlobalMailRedis::findOne(['uid' => $globalInfo->getUid(), 'boardId' => $board['id']])) {//已经找到记录
                if ($obj->isDeleted == 1) {//该条全服邮件被删除了
                    unset($result[$key]);
                }
            } else {//没有记录,如果时间正确则添加
                $time = ServerTime::getTestTime();
                if ($time >= $board['start_at'] && $time <= $board['end_at']) {
                    $globalMailRedis = new GlobalMailRedis();
                    $globalMailRedis->uid = $globalInfo->getUid();
                    $globalMailRedis->boardId = $board['id'];
                    $globalMailRedis->reward = $board['reward'];
                    $globalMailRedis->type = MailMsgType::GLOBAL_MAIL;
                    $globalMailRedis->senderName = "村长";
                    $globalMailRedis->sendTime = $board['start_at'];
                    $globalMailRedis->content = json_encode(["MailContent" => $board['content'], "MailTitle" => $board['title_config']['title']]);
                    $globalMailRedis->expire = $board['end_at'] - $board['start_at'] + $this->cacheTime;//缓存是三分钟，多加三分钟
                    $globalMailRedis->save();
                } else {//超出时间
                    unset($result[$key]);
                }

            }
        }
        $result = ArrayHelper::getColumn($result, 'id');
        foreach ($result as $key=>$value){//保证数据都是string
            $result[$key] = (string)$value;
        }
        return $result;

    }

    public function setGlobalMailStatus($boardArr, $isWatched, $isReceived, $isDeleted = 0)
    {   //只有等于1才设置
        if (empty($boardArr)) {
            return;
        }
        $globalInfo = globalInfo();
        $globalMailService = new GlobalMailService();
        $globalMailArr = $globalMailService->getAll(static::GET_ALL_DATA);
        $rewardArr = ArrayHelper::map($globalMailArr, 'id', 'reward');//json
        foreach ($boardArr as $id) {
            if ($obj = GlobalMailRedis::findOne(['uid' => $globalInfo->getUid(), 'boardId' => $id])) {
                if ($isWatched) {
                    $obj->isWatched = 1;
                }
                if ($isReceived == 1 && $obj->isReceived != 1) {//防止奖励重发
                    $obj->isReceived = 1;
                    $reward = json_decode($rewardArr[$id], true);
                    if (!empty($reward)) {
                        bean(PropertyService::class)->handleBatch($reward, FlowActionConst::MAILER_REWARD,"Global Mail");
                    }
                }
                if ($isDeleted) {
                    $obj->isDeleted = 1;
                }
                $obj->save();
            }
        }
    }

    public function getByGlobalMailArr($boardIdArr)
    {
        $globalInfo = globalInfo();
        $all = [];

        foreach ($boardIdArr as $id) {
            if ($obj = GlobalMailRedis::findOne(['uid' => $globalInfo->getUid(), 'boardId' => $id])) {
                $tmp = [];
                $tmp['id'] = $obj->boardId;
                $tmp['type'] = $obj->type;
                $tmp['senderName'] = $obj->senderName;
                $tmp['sendTime'] = $obj->sendTime;
                $tmp['isWatched'] = $obj->isWatched;
                $tmp['isReceived'] = $obj->isReceived;
                $tmp['reward'] = json_decode($obj->reward, true);
                $tmp['content'] = json_decode($obj->content, true);
                $tmp['senderUid'] = '0';
                $all[] = $tmp;
            }
        }
        return $all;

    }
}