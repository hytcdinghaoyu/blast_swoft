<?php
//namespace Swoft\Test\Cases;
//
//use App\Constants\BattleType;
//use App\Constants\Message;
//use App\Models\BattleToken;
//use Ramsey\Uuid\Uuid;
//use Swoft\App;
//
//
//class User33LeagueControllerTest extends AbstractTestCase
//{
//    /**
//     * @test
//     * @dataProvider additionInit
//     */
//    public function testInit($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/init"
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionInit()
//    {
//        return [
//            [Message::SUCCESS],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionGetInfo
//     */
//    public function testGetInfo($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/getInfo"
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionGetInfo()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionStart
//     */
//    public function testStart($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/start"
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionStart()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionLevelUpdate
//     */
//    public function testLevelUpdate($win,$level,$score,$expected)
//    {
//        $mineToken = BattleToken::findOne(['uid' => $GLOBALS['uid'], 'battleType' => BattleType::LEAGUE]);
//        if (empty($mineToken)){
//            $arr = [
//                'actions' => [
//                    '1550039646262' => [
//                        "league/start"
//                    ]
//                ],
//                "appVersion" => $GLOBALS['appVersion'],
//                "channel" => $GLOBALS['channel'],
//                "configVersion" => $GLOBALS['configVersion'],
//                "deviceId" => $GLOBALS['deviceId'],
//                "platform" => $GLOBALS['platform'],
//                "uuid" => $GLOBALS['uuid'],
//                "uid" => $GLOBALS['uid']
//            ];
//            $rawContent = json_encode($arr);
//            $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        }
//        $mineToken = BattleToken::findOne(['uid' => $GLOBALS['uid'], 'battleType' => BattleType::LEAGUE]);
//        $securityTmp = App::getBean('security');
//        $security = $securityTmp->withSecret($mineToken->secret);
//        $levelToken = $security->hash($level);
//        $scoreToken = $security->hash($score);
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/levelUpdate",
//                    $win,
//                    $levelToken,
//                    $scoreToken,
//                    1,
//                    [],
//                    [],
//                    [],
//                    false,
//                    null
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionLevelUpdate()
//    {
//        return [
//            [1,1,70000,Message::SUCCESS],
//            [1,2,70000,Message::SUCCESS],
//            [1,3,70000,Message::SUCCESS],
//            [1,4,70000,Message::SUCCESS],
//            [1,5,70000,Message::SUCCESS],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionRankingList
//     */
//    public function testRankingList($expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/rankingList"
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionRankingList()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//    /**
//     * @test
//     * @dataProvider additionPreRankingList
//     */
//    public function testPreRankingList($expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/preRankingList"
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionPreRankingList()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionRankReward
//     */
//    public function testRankReward($expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/rankReward"
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionRankReward()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionChestReward
//     */
//    public function testChestReward($type,$taskId,$season,$expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/chestReward",
//                    $type,
//                    $taskId,
//                    $season
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionChestReward()
//    {
//        return [
//            ['normal',10001,'2019.5',Message::SUCCESS],
//            ['normal1',10001,'2019.5',34004],
//            ['normal',1000111,'2019.5',34004],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionLevelBoxReward
//     */
//    public function testLevelBoxReward($level,$season,$pieceId,$expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/levelBoxReward",
//                    $level,
//                    $season,
//                    $pieceId
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionLevelBoxReward()
//    {
//        return [
//            [5,'2019.5',null,Message::SUCCESS],
//            [5,'2019.5',null,34007],
//            [6,'2019.5',null,34006]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionTaskList
//     */
//    public function testTaskList($expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/taskList",
//
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionTaskList()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionGetLevelRank
//     */
//    public function testGetLevelRank($level,$expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/getLevelRank",
//                    $level
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionGetLevelRank()
//    {
//        return [
//            [1,Message::SUCCESS]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionSeason
//     */
//    public function testSeason($expected)
//    {
//
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "league/season",
//
//                ]
//            ],
//            "appVersion" => $GLOBALS['appVersion'],
//            "channel" => $GLOBALS['channel'],
//            "configVersion" => $GLOBALS['configVersion'],
//            "deviceId" => $GLOBALS['deviceId'],
//            "platform" => $GLOBALS['platform'],
//            "uuid" => $GLOBALS['uuid'],
//            "uid" => $GLOBALS['uid']
//        ];
//        $rawContent = json_encode($arr);
//        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
//        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
//    }
//    public function additionSeason()
//    {
//        return [
//            [Message::SUCCESS]
//        ];
//    }
//}