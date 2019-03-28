<?php

namespace Swoft\Test\Cases;

use App\Constants\Message;
use App\Constants\RedisKey;
use App\Utils\ServerTime;
use Ramsey\Uuid\Uuid;


class User37TaskProgressControllerTest extends AbstractTestCase
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
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProvideInit()
    {

        return [
            [1]
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
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderGetAll()
    {

        return [
            [1,1],
//            [11, 33002],
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
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderUpdate()
    {
        return [
            [1001,11,[],false,1],
            [10011111,11,[],false,33001],
        ];
    }



    /**
     * @test
     * @dataProvider additionProviderReward
     */
    public function testReward($type,$expected)
    {
        $key = sprintf(RedisKey::TARGET_REWARD, 'daily', date('Ymd', time()), $GLOBALS['uid']);
        $res = bean('redis')->get($key);
        $resArr = json_decode($res,true);
        $taskId = 0;
        foreach ($resArr as $key=>$value){
            $taskId = $key;
        }
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
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderReward()
    {

        return [
            [1,33005]

        ];
    }


}