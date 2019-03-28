<?php

namespace App\Services;

use App\Constants\RedisKey;
use Swoft\Bean\Annotation\Bean;


/**
 * @Bean()
 */
class ReportDataService
{
    const ONLINE_ADD = 30;

    const TIME_OUT_DAY = 86400;

    /**
     * login接口上报数据
     * @return array
     */
    public static function ReportLoginInfo($isCreate, $openId, $user)
    {
        //26大区信息 27 服务器信息 28角色ID  29 角色名称
        $globalInfo = globalInfo();
        if ($isCreate == true) {//注册
            $data = [
                "openid" => $openId,
                "param" => [
                    [
                        "type" => 25,
                        "bcover" => 1,
                        "data" => (string)$user->getCreatedAt(),
                        "expires" => 0
                    ],
                    [
                        "type" => 12,
                        "bcover" => 1,
                        "data" => (string)$user->getPlatform() == 'ios' ? 0 : 1,
                        "expires" => 0
                    ],
                    [
                        "type" => 8,
                        "bcover" => 1,
                        "data" => (string)time(),
                        "expires" => 0
                    ],
                    [
                        "type" => 26,
                        "bcover" => 1,
                        "data" => "",
                        "expires" => 0
                    ],
                    [
                        "type" => 27,
                        "bcover" => 1,
                        "data" => config('serverId'),
                        "expires" => 0
                    ],
                    [
                        "type" => 28,
                        "bcover" => 1,
                        "data" => (string)$user->getUid(),
                        "expires" => 0
                    ],
                    [
                        "type" => 201,
                        "bcover" => 1,
                        "data" => (string)$user->getChannel(),
                        "expires" => 0
                    ]
                ]
            ];

        } else {//登陆
            $data = [
                "openid" => $openId,
                "param" => [
                    [
                        "type" => 8,
                        "bcover" => 1,
                        "data" => (string)time(),
                        "expires" => 0
                    ],
                    [
                        "type" => 27,
                        "bcover" => 1,
                        "data" => config('serverId'),
                        "expires" => 0
                    ],
                    [
                        "type" => 201,
                        "bcover" => 1,
                        "data" => (string)$user->getChannel(),
                        "expires" => 0
                    ]
                ]
            ];
        }
        self::pushToRedis($data);
    }


    public static function ReportUserName($userName, $openId)
    {
        $data = [
            "openid" => $openId,
            "param" => [
                [
                    "type" => 29,
                    "bcover" => 1,
                    "data" => (string)$userName,
                    "expires" => 0
                ]
            ]
        ];


        self::pushToRedis($data);
    }

    public static function ReportLevel($level, $openId)
    {
        $data = [
            "openid" => $openId,
            "param" => [
                [
                    "type" => 1,
                    "bcover" => 1,
                    "data" => (string)$level,
                    "expires" => 0
                ]
            ]
        ];


        self::pushToRedis($data);
    }

    public static function ReportOnline($openId, $uid)
    {
        $redis = bean('redis');
        $onlineTimeKey = sprintf(RedisKey::ONLINE_TIME, $uid, date("Ymd"));
        $curOnlineTime = $redis->get($onlineTimeKey);
        $nowOnlineTime = $curOnlineTime + self::ONLINE_ADD;

        $redis->SETEX($onlineTimeKey, self::TIME_OUT_DAY, $nowOnlineTime);
        $data = [
            "openid" => $openId,
            "param" => [
                [
                    "type" => 6000,
                    "bcover" => 1,
                    "data" => (string)ceil($nowOnlineTime / 60),
                    "expires" => 0
                ]
            ]
        ];
        if ($nowOnlineTime % 300 == 0) {//5分钟一次
            self::pushToRedis($data);
        }


    }

    public static function ReportPay($openId, $oneSaveAmt, $sumSaveAmt)
    {


        $data = [
            "openid" => $openId,
            "param" => [
                [
                    "type" => 46,
                    "bcover" => 1,
                    "data" => (string)time(),
                    "expires" => 0
                ],
                [
                    "type" => 44,
                    "bcover" => 1,
                    "data" => (string)$oneSaveAmt,
                    "expires" => 0
                ],
                [
                    "type" => 43,
                    "bcover" => 1,
                    "data" => (string)$sumSaveAmt,
                    "expires" => 0
                ]
            ]
        ];
        self::pushToRedis($data);


    }

    public static function pushToRedis($data)
    {
        $redis = bean('redis');
        $redis->rpush(RedisKey::QQ_DATA_UPLOAD, json_encode($data));
    }


}