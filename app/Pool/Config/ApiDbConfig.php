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
use Swoft\Db\Pool\Config\DbPoolProperties;
use Swoft\Db\Driver\Driver;

/**
 * the config of service user
 *
 * @Bean()
 */
class ApiDbConfig extends DbPoolProperties
{
    /**
     * @Value(name="${config.db.api.master.name}")
     * @var string
     */
    protected $name = '';

    /**
     * @Value(name="${config.db.api.master.minActive}")
     * @var int
     */
    protected $minActive = 5;

    /**
     * @Value(name="${config.db.api.master.maxActive}")
     * @var int
     */
    protected $maxActive = 10;

    /**
     * @Value(name="${config.db.api.master.maxWait}")
     * @var int
     */
    protected $maxWait = 20;

    /**
     * @Value(name="${config.db.api.master.maxIdleTime}")
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * @Value(name="${config.db.api.master.maxWaitTime}")
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * @Value(name="${config.db.api.master.timeout}")
     * @var int
     */
    protected $timeout = 3;

    /**
     * the addresses of connection
     *
     * <pre>
     * [
     *  '127.0.0.1:88',
     *  '127.0.0.1:88'
     * ]
     * </pre>
     *
     * @Value(name="${config.db.api.master.uri}")
     * @var array
     */
    protected $uri = [];

    /**
     * the default driver is consul mysql
     *
     * @Value(name="${config.db.api.master.driver}")
     * @var string
     */
    protected $driver = Driver::MYSQL;

    /**
     * @Value(name="${config.db.api.master.strictType}")
     * @var bool
     */
    protected $strictType = false;
}
