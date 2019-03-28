<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$globalDb = $params['mysql']['globalDb_zone'];
$apiDb = $params['mysql']['db'];

return [
    'global' => [
        'master' => [
            'name'        => 'master',
            'uri'         => [
                "{$globalDb['host']}:{$globalDb['port']}/{$globalDb['dbname']}?user={$globalDb['username']}&password={$globalDb['password']}&charset=utf8"
            ],
            'minActive'   => 8,
            'maxActive'   => 8,
            'maxWait'     => 8,
            'timeout'     => 8,
            'maxIdleTime' => 60,
            'maxWaitTime' => 3,
        ]
    ],
    'api'    => [
        'master' => [
            'name'        => 'api_master',
            'uri'         => [
                "{$apiDb['host']}:{$apiDb['port']}/{$apiDb['dbname']}?user={$apiDb['username']}&password={$apiDb['password']}&charset=utf8"
            ],
            'minActive'   => 8,
            'maxActive'   => 8,
            'maxWait'     => 8,
            'timeout'     => 8,
            'maxIdleTime' => 60,
            'maxWaitTime' => 3,
        ]
    ],
];