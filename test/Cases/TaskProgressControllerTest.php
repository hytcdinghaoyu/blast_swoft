<?php

namespace Swoft\Test\Cases;

use App\Constants\Message;
use Ramsey\Uuid\Uuid;


class TaskProgressControllerTest extends AbstractTestCase
{

    /**
     * @test
     * @dataProvider additionProvideInit
     */
    public function testInit($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "taskProgress/init"
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

    public function additionProviderSubmit()
    {

        return [
            [1]
        ];
    }


    /**
     * @test
     * @dataProvider additionProviderUpdate
     */
    public function testUpdate(int $targetId, int $incNum, array $extra = [], bool $isReset = false,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "taskProgress/update",
                    $targetId,
                    $incNum,
                    $extra,
                    $isReset
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

    public function additionProviderUpdate()
    {
        return [
            [1001,2,[],false,1],
            [10011111,11,[],false,33001],
        ];
    }


    /**
     * @test
     * @dataProvider additionProviderGetAll
     */
    public function testGetAll($type, $expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "taskProgress/getAll",
                    $type
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

    public function additionProviderGetAll()
    {

        return [
            [1,1],
            [11, 33002],
        ];
    }
    /**
     * @test
     * @dataProvider additionProviderReward
     */
    public function testReward($type, $taskId,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "taskProgress/reward",
                    $type,
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
        $response = $this->request('POST', '/', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderReward()
    {

        return [
            [1,302,1],
            [1,302,33004],
            [1,303,33005]
        ];
    }


}