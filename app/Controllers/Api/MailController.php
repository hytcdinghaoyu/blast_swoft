<?php
/**
 * This file is part of Swoft.
 *
 * @link    https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Controllers\Api;

use App\Constants\MailProcessAction;
use App\Models\Dao\CenterRewardPackageDao;
use App\Models\MailMsg;
use App\Constants\MailMsgType;
use App\Controllers\CommonController;
use App\Services\GlobalMailService;
use App\Services\MailerService;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Bean\Annotation\Inject;


/**
 * 用户模块.
 * @Controller(prefix="/sysmail")
 */
class MailController extends CommonController
{
    
    /**
     * @Inject
     * @var MailerService
     */
    private $mailer;
    


    /**
     *
     * @RequestMapping(route="poll")
     * 轮询用户最新的消息id
     * 包含全服邮件
     */
    public function actionPoll(int $uid)
    {
        //全服邮件是读取centerboard
        $globalMailService = bean(GlobalMailService::class);
        $mailService = bean(MailerService::class);

        $globalMailArr = $globalMailService->getAll($globalMailService::GET_ID_ARR);

        //其他邮件正常逻辑
        $ids = $mailService->fetchMsgIdsByUid($uid);
        if (count($globalMailArr) >= $mailService::MAX_MAIL_NUM) {//全服邮件数量大于一百
            $resArr = array_slice($globalMailArr, 0, $mailService::MAX_MAIL_NUM);
        } else{
            $normalMailCount = $mailService::MAX_MAIL_NUM - count($globalMailArr);
            $normalMailOffset = min($normalMailCount,count($ids));
            $tmpArr = array_slice($ids, 0, $normalMailOffset);
            $resArr = array_merge($globalMailArr,$tmpArr);
        }

        return $this->returnData(
            ['messageIds' => $resArr]
        );
    }

    /**
     * @RequestMapping(route="operatemail")
     * 邮件批量操作
     */
    public function actionOperateMail($mailArr, $isWatched, $isReceived, $isDeleted = 0)
    {
        //邮件分为全服邮件（读数据库）,正常邮件(读redis)
        $globalMailService = bean(GlobalMailService::class);
        $mailService = bean(MailerService::class);
        $globalMailArr = $globalMailService->getAll($globalMailService::GET_ID_ARR);

        $boardArr = [];
        $normalMailArr = [];
        foreach ($mailArr as $mailId) {
            if (in_array($mailId, $globalMailArr) && $mailId <= $mailService::ID_OFFSET) {
                $boardArr[] = $mailId;
            } else {
                $normalMailArr[] = $mailId;
            }
        }
        $mailService->setNormalMailStatus($normalMailArr, $isWatched, $isReceived, $isDeleted);

        $globalMailService->setGlobalMailStatus($boardArr, $isWatched, $isReceived, $isDeleted);

        return $this->returnSuccess();

    }


    /**
     *
     * 根据msgId查询消息详情
     * @RequestMapping(route="fetchid")
     * @param array $msgIdArr
     * @return array
     */
    public function actionFetchId(array $msgIdArr = [])
    {
        //邮件分为全服邮件（读数据库）,正常邮件(读redis)
        $globalMailService = bean(GlobalMailService::class);
        $mailService = bean(MailerService::class);
        $globalMailArr = $globalMailService->getAll($globalMailService::GET_ID_ARR);

        $boardArr = [];
        $normalMailArr = [];
        foreach ($msgIdArr as $mailId) {
            if (in_array($mailId, $globalMailArr) && $mailId <= $mailService::ID_OFFSET) {
                $boardArr[] = $mailId;
            } else {
                $normalMailArr[] = $mailId;
            }
        }
        $messages = $globalMails = [];
        if (!empty($normalMailArr)){
            $messages = $mailService->findAllByMsgIds($normalMailArr);
        }
        if (!empty($boardArr)){
            $globalMails = $globalMailService->getByGlobalMailArr($boardArr);
        }

        $messages = array_merge($messages, $globalMails);
        return $this->returnData(
            ['messages' => $messages]
        );
    }

    /**
     * @RequestMapping(route="process")
     * 处理消息
     * @param int $msgId
     * @param int $action
     */
    public function actionProcess(int $uid, int $msgId, int $action)
    {

        $mailService = bean(MailerService::class);

        if (!$mailService->isValidMsgId($uid, $msgId)) {
            return $this->returnData(['msgId' => $msgId]);
        }

        $msg = $mailService->findOneById($msgId);
        if (!$msg) {
            return $this->returnData(['msgId' => $msgId]);
        }

        //同意，处理附件中的奖励
        if ($action == MailProcessAction::AGREE) {
            $msg = $mailService->handleReward($msg);
            if (isset($msg['errCode'])) {
                return $this->returnError($msg['errCode']);
            }
        }

        $mailService->delete($uid, $msgId);

        return $this->returnData(['msg' => $msg ? $msg : []]);

    }

    /**
     * @RequestMapping(route="gmsendreward")
     * @param int $uid
     * @param int $rewardId
     * @return array
     */
    public function actionGmSendReward(int $uid, int $rewardId)
    {

        $row = bean(CenterRewardPackageDao::class)->findOneByRewardId($rewardId);
        $reward = json_decode($row['contain_list'],true);
        $msg = new MailMsg();
        $msg->setSenderUid(0);
        $msg->setReceiverUid($uid);
        $msg->setType(MailMsgType::SYSTEM_REWARD);
        if (is_array($reward)) {
            $msg->setReward($reward);
        }
        $this->mailer->send($msg);

        return $this->returnSuccess();
    }
    
}
