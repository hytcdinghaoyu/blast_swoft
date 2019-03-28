<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;

/**
 * RedisPoolConfig
 *
 * @Bean()
 */
class RedisPoolConfig extends \Swoft\Redis\Pool\Config\RedisPoolConfig
{
    /**
     * @Value(name="${config.cache.redis.db}")
     * @var int
     */
    protected $db = 0;

    /**
     * @Value(name="${config.cache.redis.prefix}")
     * @var string
     */
    protected $prefix = '';
}