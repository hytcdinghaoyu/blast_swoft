<?php
namespace App\Services;


use App\Constants\RedisKey;

/**
 * @package App\Services
 */
class MainProgressService extends TaskProgressService
{
    public function getRewardConfigKey()
    {
        return sprintf(RedisKey::TARGET_REWARD, 'main', 0,  globalInfo()->getUid());
    }

    public function getPrevRewardConfigKey()
    {
        return sprintf(RedisKey::TARGET_REWARD, 'main', 0,  globalInfo()->getUid());
    }

    public function getProgressKey()
    {
        return sprintf(RedisKey::TARGET_PROGRESS, 'main', 0, globalInfo()->getUid());
    }

    public function getConfigName()
    {
        return 'questMainTask';
    }

    public function expireAt()
    {
        return null;
    }
}