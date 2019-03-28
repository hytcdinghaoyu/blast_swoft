<?php

namespace App\Services;

use App\Constants\RedisKey;


class DailyProgressService extends TaskProgressService
{
    public function getRewardConfigKey()
    {
        return sprintf(RedisKey::TARGET_REWARD, 'daily', date('Ymd', $this->_testTime), globalInfo()->getUid());
    }

    public function getPrevRewardConfigKey()
    {
        return sprintf(RedisKey::TARGET_REWARD, 'daily', date('Ymd', $this->_testTime - 86400), globalInfo()->getUid());
    }

    public function getProgressKey()
    {
        return sprintf(RedisKey::TARGET_PROGRESS, 'daily', date('Ymd', $this->_testTime), globalInfo()->getUid());
    }

    public function getConfigName()
    {
        return 'questDailyTask';
    }

    /**
     * 次日零点过期
     * @return null
     */
    public function expireAt()
    {
        $date = date('Ymd', strtotime("+1 days", $this->_testTime));
        return strtotime($date);
    }
}
