<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'serverDispatcher'     => [
        'middlewares' => [
            \App\Middlewares\ModulesDispatchMiddleware::class,
            \App\Middlewares\DecryptMiddleware::class,
            \App\Middlewares\MultiActionsMiddleware::class,
            \App\Middlewares\EncryptMiddleware::class,
        ],
    ],
    \Swoft\Auth\Mapping\AuthManagerInterface::class=>[
        'class'=>\Swoft\Auth\AuthManager::class
    ],
    'rovioAccount' => [
        'class' => \App\Auth\RovioAccount::class
    ],
    \Swoft\Auth\Mapping\AuthorizationParserInterface::class => [
        'class' => \App\Auth\TokenHeaderParser::class,
    ],
    'httpRouter' => [
        'ignoreLastSlash' => false,
        'tmpCacheNumber'  => 1002,
        'matchAll'        => '',
    ],
    'requestParser'        => [
        'parsers' => [

        ],
    ],
    'view'                 => [
        'viewsPath' => '@resources/views/',
    ],
    'cache'                => [
        'driver' => 'redis',
    ],
    'redis'                => [
        'class'    => \Swoft\Redis\Redis::class,
        'poolName' => 'redis'
    ],
    'yiiSecurity'          => [
        'class' => \yii\base\Security::class
    ],
    'security'             => [
        'class' => App\Utils\Security::class
    ],
    'reflectionUserParams' => [
        'class' => App\Utils\ReflectionUserParams::class
    ],
    'commonVar'            => [
        'class' => App\Utils\CommonVar::class
    ]
];
