<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */



class InviteControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProviderInit
     */

    public function init($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "invite/init",

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


    public function additionProviderInit()
    {
        return [
           [Message::SUCCESS]

        ];
    }


    /**
     * @test
     * @dataProvider additionProviderSendInvite
     */

    public function sendInvite($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "invite/sendInvite",

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


    public function additionProviderSendInvite()
    {
        return [
            [Message::SUCCESS],
            [Message::SUCCESS],
            [Message::SUCCESS]

        ];
    }

    /**
     * @test
     * @dataProvider additionProviderProgressReward
     */

    public function progressReward($progress,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "invite/progressReward",
                    $progress

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


    public function additionProviderProgressReward()
    {
        return [
            [3,Message::SUCCESS],
            [3,35501],
            [1,35502]

        ];
    }
}
