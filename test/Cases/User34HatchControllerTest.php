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
//class User34HatchControllerTest extends AbstractTestCase
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
//                    "hatch/init"
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
//    public function additionInit()
//    {
//        return [
//            [Message::SUCCESS],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider additionGetReward
//     */
//    public function testGetReward($expected)
//    {
//        $arr = [
//            'actions' => [
//                '1550039646262' => [
//                    "hatch/getReward"
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
//    public function additionGetReward()
//    {
//        return [
//            [Message::SUCCESS],
//        ];
//    }
//}