<?php

namespace App\Services;

use App\Constants\LeaderBoardType;
use App\Constants\RedisKey;
use App\Models\Dao\UserInfoDao;
use yii\helpers\ArrayHelper;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;

/**
 * @Bean()
 */
class LeaderBoardService
{

    /**
     * @Inject
     * @var UserInfoDao
     */
    private $userInfoDao;

    // 排行榜列表缓存时间
    const SUBMIT_TYPE_INCR = 'incr';

    const SUBMIT_TYPE_ADD = 'add';
    /**
     * 以累加的方式提交排行榜分数
     */
    public function updateByType($uid, $type, $score)
    {
        $redisKey = $this->getKey($type);
        bean('redis')->zIncrBy($redisKey, $score, $uid);
        return true;
    }

    /**
     * 从指定排行榜中读取指定用户的个人信息
     */
    public function getCustomerRankInfoByLeaderBoard($type, $uid_arr)
    {

        $redisKey = $this->getKey($type);
        $rankInfo = [];
        $uid_arr = array_unique($uid_arr);

        $user_info_arr = $this->userInfoDao->getAllByUidArr(array_values($uid_arr));
        $user_info_index = ArrayHelper::index($user_info_arr, 'uid');
        foreach ($uid_arr as $uid) {
            $customerInfo = isset($user_info_index[$uid]) ? $user_info_index[$uid] : false;
            if ($customerInfo === false) {
                continue;
            }
            $item = [];
            $item['accountId'] = $uid;
            $item['rank'] = bean('redis')->zRevRank($redisKey, $uid);
            if (!isset($item['rank'])) {
                continue;
            }
            $item['rank']++;
            $item['points'] = bean('redis')->zScore($redisKey, $uid);
            $item['userName'] = $customerInfo['username'];
            $item['avatarUrl'] = $customerInfo['avatar'];
            $rankInfo[] = $item;
        }

        return $rankInfo;
    }

    /**
     * 从指定排行榜$leaderBoard中获取从$offset开始的$amount个用户的信息
     */
    public function getListByAmount($type, $amount, $offset)
    {

        $redisKey = $this->getKey($type);
        $baseFrom = $offset;
        $rank = [];
        $rankInfo = bean('redis')->zrevrange($redisKey, $offset, $amount - 1, true);
        $user_info_arr = $this->userInfoDao->getAllByUidArr(array_keys($rankInfo));
        $user_info_index = ArrayHelper::index($user_info_arr, 'uid');
        foreach ($rankInfo as $uid => $score) {
            $item = [];
            $customerInfo = isset($user_info_index[$uid]) ? $user_info_index[$uid] : false;
            if ($customerInfo === false) {
                continue;
            }
            $item['accountId'] = $uid;
            $item['points'] = (int)$score;
            $item['userName'] = $customerInfo['username'];
            $item['avatarUrl'] = $customerInfo['avatar'];
            $rank[$baseFrom++] = $item;
        }
        return $rank;
    }

    public function getKey($type)
    {

        if ($type == LeaderBoardType::DAILY) {
            $type = date('ymd', time());
        } elseif ($type == LeaderBoardType::WEEKLY) {
            $type = date('yW', time());
        }

        return sprintf(RedisKey::RANKINGS, $type);
    }


}