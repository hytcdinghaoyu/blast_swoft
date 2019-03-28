<?php

namespace App\Services;

use App\Constants\RedisKey;


class WeeklyProgressService extends TaskProgressService
{
    public function getRewardConfigKey()
    {
        return sprintf(RedisKey::TARGET_REWARD, 'weekly', date('YW', $this->_testTime),  globalInfo()->getUid());
    }

    public function getPrevRewardConfigKey()
    {
        return sprintf(RedisKey::TARGET_REWARD, 'weekly', date('YW', $this->_testTime - 7 * 86400),  globalInfo()->getUid());
    }

    public function getProgressKey()
    {
        return sprintf(RedisKey::TARGET_PROGRESS, 'weekly', date('YW', $this->_testTime),  globalInfo()->getUid());
    }

    public function getConfigName()
    {
        return 'questWeekTask';
    }

    /**
     * 下一周零点过期
     * @return null
     */
    public function expireAt()
    {
        return $this->_testTime + 8 * 24 * 3600;
    }
}
