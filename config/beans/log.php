<?php
/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$flushInterval = 100;
$flushRequest = true;

$commonLog = [
    'logger'             => [
        'name'          => APP_NAME,
        'class'         => App\Utils\Logger::class,
        'enable'        => env('LOG_ENABLE', false),
        'flushInterval' => $flushInterval,
        'flushRequest'  => $flushRequest,
        'handlers'      => [
            '${applicationHandler}',
            '${dataHandler}',
            '${noticeHandler}',
        ],
    ],
    'applicationHandler' => [
        'class'        => App\Utils\FileHandler::class,
        'logFile'      => '@runtime/logs/app.log',
        'segmentation' => 'YmdH',
        'formatter'    => '${lineFormatter}',
        'levels'       => [
            \Swoft\Log\Logger::ERROR,
            \Swoft\Log\Logger::WARNING,
        ],
    ],
    'dataHandler'        => [
        'class'        => App\Utils\FileHandler::class,
        'logFile'      => '@runtime/logs/dataLog.log',
        'segmentation' => 'YmdH',
        'formatter'    => '${lineFormatter}',
        'levels'       => [
            \Swoft\Log\Logger::INFO,
        ],
    ],
    'noticeHandler'      => [
        'class'        => App\Utils\FileHandler::class,
        'logFile'      => '@runtime/logs/notice.log',
        'segmentation' => 'YmdH',
        'formatter'    => '${lineFormatter}',
        'levels'       => [
            \Swoft\Log\Logger::NOTICE,
            \Swoft\Log\Logger::DEBUG,
            \Swoft\Log\Logger::TRACE,
        ],
    ],
    'lineFormatter'      => [
        'class'      => \Monolog\Formatter\LineFormatter::class,
        'format'     => '%datetime% [%clientIp%] [%level_name%] [%category%] %messages%',
        'dateFormat' => 'Y-m-d H:i:s',
        'allowInlineLineBreaks' => true
    ],
];

$actionLog = [
    'actionLogger'  => [
        'name'          => APP_NAME,
        'class'         => App\Utils\Logger::class,
        'enable'        => env('LOG_ENABLE', false),
        'flushInterval' => $flushInterval,
        'flushRequest'  => $flushRequest,
        'handlers'      => [
            '${actionHandler}',
        ],
    ],
    'actionHandler' => [
        'class'        => App\Utils\FileHandler::class,
        'logFile'      => '@runtime/logs/action.log',
        'segmentation' => 'YmdH',
        'formatter'    => '${lineFormatter}',
        'levels'       => [
            \Swoft\Log\Logger::INFO,
        ],
    ]
];

$tlogLogger = [
    'tlogLogger'  => [
        'name'          => APP_NAME,
        'class'         => \App\Utils\Logger::class,
        'enable'        => env('LOG_ENABLE', false),
        'flushInterval' => 1,
        'flushRequest'  => $flushRequest,
        'handlers'      => [
            '${tlogFileHandler}',
            '${tlogUdpHandler}',
        ],
    ],
    'tlogFileHandler' => [
        'class'        => App\Utils\FileHandler::class,
        'logFile'      => '@runtime/logs/tlog.log',
        'segmentation' => 'YmdH',
        'formatter'    => '${tlogFormatter}',
        'levels'       => [
            \Swoft\Log\Logger::INFO,
        ],
    ],
    'tlogUdpHandler' => [
        'class'        => \App\Base\UdpHandler::class,
        'formatter'    => '${tlogFormatter}',
        'ip'           => '127.0.0.1',
        'port'         =>  63001,
        'levels'       => [
            \Swoft\Log\Logger::INFO,
        ],
    ],
    'tlogFormatter'      => [
        'class'      => \Monolog\Formatter\LineFormatter::class,
        'format'     => '%messages%',
    ],
];

return array_merge($commonLog, $actionLog, $tlogLogger);
