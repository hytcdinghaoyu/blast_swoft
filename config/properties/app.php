<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$params = json_decode(file_get_contents(BASE_PATH . '/../params.json'), true);

if ($params['logPath']['game_srv'] != '') {
    \Swoft\App::setAlias('@runtime', $params['logPath']['game_srv']);
}

return array_merge([
    'env'          => env('APP_ENV', 'test'),
    'debug'        => env('APP_DEBUG', false),
    'version'      => '1.0',
    'autoInitBean' => true,
    'bootScan'     => [
        'App\Exception',
        'App\Commands',
        'App\Boot'
    ],
    'excludeScan'  => [

    ],
    'I18n'         => [
        'sourceLanguage' => '@root/resources/messages/',
    ],
    'db'           => require __DIR__ . DS . 'db.php',
    'cache'        => require __DIR__ . DS . 'cache.php',
    'service'      => require __DIR__ . DS . 'service.php',
    'breaker'      => require __DIR__ . DS . 'breaker.php',
    'provider'     => require __DIR__ . DS . 'provider.php',
    'secret'       => '05777b28db2723633c5648d6576555a4',
    'components' => [
        'custom' => [
            'Swoftx\\Aop\\Cacheable\\',
        ],
    ]
], $params);
