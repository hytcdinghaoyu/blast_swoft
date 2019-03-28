<?php

namespace Swoft\Test\Cases;

use App\Constants\Message;
use Ramsey\Uuid\Uuid;


class TaskControllerTest extends AbstractTestCase
{

    /**
     * @test
     * @dataProvider additionProvideQuestReward
     */
    public function testQuestReward($isMaster,$taskId,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/questReward",
                    $isMaster,
                    $taskId
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderQuestReward()
    {

        return [
            [true,20004,1],
            [true,20004,31303],
            [true,2000411,31301],
        ];
    }


    /**
     * @test
     * @dataProvider additionProviderMasterList
     */
    public function testMasterList($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "taskProgress/masterList"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderMasterList()
    {
        return [
            [1]
        ];
    }


    /**
     * @test
     * @dataProvider additionProviderQuestList
     */
    public function testQuestList($dailyNum, $expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/questList",
                    $dailyNum
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderQuestList()
    {

        return [
            [1,1]
        ];
    }
    /**
     * @test
     * @dataProvider additionProviderDailyList
     */
    public function testDailyList($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/dailyList"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderDailyList()
    {

        return [
            [1]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderRewardByType
     */
    public function testRewardByType($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/rewardByType"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderRewardByType()
    {

        return [
            [11001,"daily",1],
            [11001,"daily",31303]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderSign
     */
    public function testSign($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/sign"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderSign()
    {

        return [
            [1],
            [31305]
        ];
    }
    /**
     * @test
     * @dataProvider additionProviderRedoSign
     */
    public function testRedoSign($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/redoSign"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderRedoSign()
    {

        return [
            [1]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderGetConfig
     */
    public function testGetConfig($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/getConfig"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderGetConfig()
    {

        return [
            ["dailySignReward",1]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderSignInfo
     */
    public function testSignInfo($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/signInfo"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderSignInfo()
    {

        return [
            [1]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderUnlimitedLife
     */
    public function testUnlimitedLife($id,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/unlimitedlife",
                    $id
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderUnlimitedLife()
    {

        return [
            [1542,1]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderGetProgress
     */
    public function testGetProgress($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/getProgress"
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderGetProgress()
    {

        return [
            [1]
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderUploadProgress
     */
    public function testUploadProgress($progress,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "task/uploadProgress",
                    $progress
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid" => $GLOBALS['uuid'],
            "uid" => $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderUploadProgress()
    {

        return [
            [1,1]
        ];
    }


}