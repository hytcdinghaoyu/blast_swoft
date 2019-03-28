<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */



class NewUserActivityControllerTest extends AbstractTestCase
{
      /**
     * @test
     * @dataProvider additionProviderGetStatus
     */

    public function getStatus1($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "newUserActivity/getStatus",

                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid"=> $GLOBALS['uuid'],
            "uid"=> $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);

    }


    public function additionProviderGetStatus()
    {
        return [
            [Message::SUCCESS]

        ];
    }


    /**
     * @test
     * @dataProvider additionProviderGetInfo
     */

    public function getInfo($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "newUserActivity/getInfo",

                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid"=> $GLOBALS['uuid'],
            "uid"=> $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);

    }


    public function additionProviderGetInfo()
    {
        return [
            [Message::SUCCESS]

        ];
    }

    /**
     * @test
     * @dataProvider additionProviderReward
     */

    public function reward($reward_type,$reward_id,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "newUserActivity/reward",
                    $reward_type,
                    $reward_id

                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform'],
            "uuid"=> $GLOBALS['uuid'],
            "uid"=> $GLOBALS['uid']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON, ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);

    }


    public function additionProviderReward()
    {
        return [
            ['normal',10101,Message::SUCCESS],
            ['normal',10101,32003]

        ];
    }
}
