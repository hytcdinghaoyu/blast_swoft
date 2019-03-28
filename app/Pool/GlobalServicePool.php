<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Pool;

use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use App\Pool\Config\GlobalDbConfig;
use Swoft\Db\Pool\DbPool;

/**
 * the pool of user service
 *
 * @Pool(name="global.master")
 */
class GlobalServicePool extends DbPool
{
    /**
     * @Inject()
     *
     * @var GlobalDbConfig
     */
    protected $poolConfig;
}