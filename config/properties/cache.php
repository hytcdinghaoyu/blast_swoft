<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
$redis = $params['redis']['redis'];
return [
    'redis' => [
        'name'        => 'redis',
        'uri'         => [
            "{$redis['hostname']}:{$redis['port']}"
        ],
        'minActive'   => 8,
        'maxActive'   => 8,
        'maxWait'     => 8,
        'maxWaitTime' => 3,
        'maxIdleTime' => 60,
        'timeout'     => 8,
        'db'          => 0,
        'prefix'      => "{$params['zoneId']}:",
        'serialize'   => 0,
    ],
];