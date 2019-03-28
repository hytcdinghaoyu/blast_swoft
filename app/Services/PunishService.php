<?php


namespace App\Services;

use App\Constants\PunishType;
use App\Models\Dao\UserInfoDao;
use App\Models\Entity\UserInfo;
use App\Models\IdipPunishInfo;
use App\Models\PunishDetail;
use Swoft\Bean\Annotation\Bean;



/**
 * @Bean()
 */
class PunishService
{


    /**
     * login接口中处理封号相关
     * @return array
     */
    public static function LoginPunishInfo($user, $OpenId)
    {

        if (empty($user)) {//找不到用户，没有被封号
            return [];
        }
        //$user里面只有uid与uuid
        $idipPunishInfo = new IdipPunishInfo();
        $punishInfo = $idipPunishInfo->findOne($OpenId);

        if ($punishInfo){
            if ($punishInfo->banUser == 1) {//被封号

                $punishDetail = PunishDetail::findOne(["openId" => $OpenId, "type" => PunishType::BAN_USER]);

                if ($punishDetail) {//封号详情中找到了说明封号未过期
                    return ['banReason'=>$punishDetail->reason];
                } else {//封号到期自动解封
                    $punishInfo->banUser = 0;
                    $punishInfo->save();

                    //改数据库字段
                    UserInfoDao::setStatusOnline($user->getUid());
                    return [];

                }
            }
        }



        return [];
    }

    public static function OnlinePunishInfo(UserInfo $userInfo,$OpenId)
    {
        if ($userInfo->getStatus() == UserInfoDao::STATUS_BANNED_TMP || $userInfo->getStatus() == UserInfoDao::STATUS_BANNED_FOREVER) {
            $punishDetail = PunishDetail::findOne(["openId"=>$OpenId,"type"=>PunishType::BAN_USER]);
            if($punishDetail){
                $dateTime =  date("Y-m-d H:i:s",$punishDetail->end_at);
                return $punishDetail->reason."|".$dateTime;
            }
        }
        //不是封号的先把状态置为正常
        UserInfoDao::setStatusOnline($userInfo->getUid());

        $punishInfo = IdipPunishInfo::findOne($OpenId);

        if (empty($punishInfo)){
            return "";
        }
        $returnArr = [];
        $outFlag = false;
        if ($punishInfo->banRankingLeague) {
            $punishDetail = PunishDetail::findOne(["openId"=>$OpenId,"type"=>PunishType::BAN_RANKING_LEAGUE]);
            if($punishDetail){
                $dateTime =  date("Y-m-d H:i:s",$punishDetail->end_at);
                $outFlag = true;
                $returnArr[$punishDetail->start_at] = $punishDetail->reason."|".$dateTime;
            }else{//处罚到期
                $punishInfo->banRankingLeague = 0;
                $punishInfo->save();
            }
        }
        if ($punishInfo->banRankingDailyMission) {
            $punishDetail = PunishDetail::findOne(["openId"=>$OpenId,"type"=>PunishType::BAN_RANKING_DAILY_MISSION]);
            if($punishDetail){
                $dateTime =  date("Y-m-d H:i:s",$punishDetail->end_at);
                $outFlag = true;
                $returnArr[$punishDetail->start_at] = $punishDetail->reason."|".$dateTime;
            }else{//处罚到期
                $punishInfo->banRankingDailyMission = 0;
                $punishInfo->save();
            }
        }
        if ($punishInfo->banRankingProsperity) {
            $punishDetail = PunishDetail::findOne(["openId"=>$OpenId,"type"=>PunishType::BAN_RANKING_PROSPERITY]);
            if($punishDetail){
                $dateTime =  date("Y-m-d H:i:s",$punishDetail->end_at);
                $outFlag = true;
                $returnArr[$punishDetail->start_at] = $punishDetail->reason."|".$dateTime;
            }else{//处罚到期
                $punishInfo->banRankingProsperity = 0;
                $punishInfo->save();
            }
        }
        if ($punishInfo->zeroIncome) {
            $punishDetail = PunishDetail::findOne(["openId"=>$OpenId,"type"=>PunishType::ZERO_INCOME]);
            if($punishDetail){
                $dateTime =  date("Y-m-d H:i:s",$punishDetail->end_at);
                $outFlag = true;
                $returnArr[$punishDetail->start_at] = $punishDetail->reason."|".$dateTime;
            }else{//处罚到期
                $punishInfo->zeroIncome = 0;
                $punishInfo->save();
            }
        }

        if ($outFlag){
            $tmpArr = array_keys($returnArr);
            $maxStartAt = max($tmpArr);
            return $returnArr[$maxStartAt];
        }
        return "";

    }
}