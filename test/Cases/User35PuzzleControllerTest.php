<?php
namespace Swoft\Test\Cases;

use App\Constants\BattleType;
use App\Constants\Message;
use App\Models\BattleToken;
use Ramsey\Uuid\Uuid;
use Swoft\App;


class User35PuzzleControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionRewardPiece
     */
    public function testRewardPiece($pieceArr,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "puzzle/rewardPiece",
                    $pieceArr
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
    public function additionRewardPiece()
    {

        return [
            [[1601=>2,1602=>1,1603=>2,1604=>1,1605=>2,1606=>1,1607=>2,1608=>1,1609=>2],
                Message::SUCCESS]
        ];
    }

    /**
     * @test
     * @dataProvider additionConsume
     */
    public function testConsume($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "puzzle/consume"
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
    public function additionConsume()
    {

        return [
            [Message::SUCCESS]
        ];
    }

    /**
     * @test
     * @dataProvider additionInfo
     */
    public function testInfo($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "puzzle/info"
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
    public function additionInfo()
    {

        return [
            [Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionRequest
     */
    public function testRequest($fuid,$pieceId,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "puzzle/request",
                    $fuid,
                    $pieceId
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
    public function additionRequest()
    {

        return [
            [27,1601,Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionSend
     */
    public function testSend($fuid,$pieceId,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "puzzle/request",
                    $fuid,
                    $pieceId
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
    public function additionSend()
    {

        return [
            [27,1601,Message::SUCCESS],
            [27,1601,Message::SUCCESS],
            [27,1601,34006]
        ];
    }
}