<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */



class ShareControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProviderReward
     */

    public function reward($shareType,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "share/reward",
                    $shareType

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
            ['normalLevel',Message::SUCCESS],
            [1,35503]

        ];
    }
    /**
     * @test
     * @dataProvider additionProviderGetShareNum
     */

    public function getShareNum($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "share/getShareNum",

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


    public function additionProviderGetShareNum()
    {
        return [
            [Message::SUCCESS],

        ];
    }


}
