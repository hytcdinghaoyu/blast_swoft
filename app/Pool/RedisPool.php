<?php
/**
 * This file is part of Swoft.
 *
 * @link    https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Pool;

use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use App\Pool\Config\RedisPoolConfig;

/**
 * RedisPool
 *
 * @Pool("redis")
 */
class RedisPool extends \Swoft\Redis\Pool\RedisPool
{
    /**
     * @Inject()
     * @var RedisPoolConfig
     */
    public $poolConfig;
}