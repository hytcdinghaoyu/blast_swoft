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
//class User36TaskControllerTest extends AbstractTestCase
//{
//    /**
//     * @test
//     * @dataProvider additionProviderQuestReward
//     */
//    public function testQuestReward($isMaster, $taskId,$expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/questReward",
//                    $isMaster,
//                    $taskId
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
//    public function additionProviderQuestReward()
//    {
//
//        return [
//            [true,20004,Message::SUCCESS]
//        ];
//    }
//    /**
//     * @test
//     * @dataProvider additionProviderMasterList
//     */
//    public function testMasterList($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/masterList"
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
//
//    public function additionProviderMasterList()
//    {
//        return [
//            [1]
//        ];
//    }
//
//
//    /**
//     * @test
//     * @dataProvider additionProviderQuestList
//     */
//    public function testQuestList($dailyNum, $expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/questList",
//                    $dailyNum
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
//
//    public function additionProviderQuestList()
//    {
//
//        return [
//            [1,1]
//        ];
//    }
//    /**
//     * @test
//     * @dataProvider additionProviderDailyList
//     */
//    public function testDailyList($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/dailyList"
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
//
//    public function additionProviderDailyList()
//    {
//
//        return [
//            [1]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderRewardByType
//     */
//    public function testRewardByType($taskId, $type,$expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/rewardByType",
//                    $taskId,
//                    $type
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
//
//    public function additionProviderRewardByType()
//    {
//
//        return [
//            [11001,"daily",1],
//            [11001,"daily",31303]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderSign
//     */
//    public function testSign($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/sign"
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
//
//    public function additionProviderSign()
//    {
//
//        return [
//            [1],
//            [31305]
//        ];
//    }
//    /**
//     * @test
//     * @dataProvider additionProviderRedoSign
//     */
//    public function testRedoSign($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/redoSign"
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
//
//    public function additionProviderRedoSign()
//    {
//
//        return [
//            [31306]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderGetConfig
//     */
//    public function testGetConfig($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/getConfig"
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
//
//    public function additionProviderGetConfig()
//    {
//
//        return [
//            [1]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderSignInfo
//     */
//    public function testSignInfo($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/signInfo"
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
//
//    public function additionProviderSignInfo()
//    {
//
//        return [
//            [1]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderUnlimitedLife
//     */
//    public function testUnlimitedLife($id,$expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/unlimitedlife",
//                    $id
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
//
//    public function additionProviderUnlimitedLife()
//    {
//
//        return [
//            [1542,1]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderGetProgress
//     */
//    public function testGetProgress($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/getProgress"
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
//
//    public function additionProviderGetProgress()
//    {
//
//        return [
//            [1]
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionProviderUploadProgress
//     */
//    public function testUploadProgress($progress,$expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "task/uploadProgress",
//                    $progress
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
//
//    public function additionProviderUploadProgress()
//    {
//
//        return [
//            [1,1]
//        ];
//    }
//}