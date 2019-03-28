<?php
namespace Swoft\Test\Cases;

use App\Constants\BattleType;
use App\Constants\Message;
use App\Models\BattleToken;
use Ramsey\Uuid\Uuid;
use Swoft\App;


class User32RewardPackageControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProvideRecieve
     */
    public function testRecieve($rewardId,$origin,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "rewardPackage/recieve",
                    $rewardId,
                    $origin,
                    0
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
    public function additionProvideRecieve()
    {

        return [
            [1000151,28,Message::SUCCESS],
//            [1100151,28,Message::SUCCESS],
//            [1000151,27,50002]
        ];
    }
}