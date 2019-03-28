<?php
namespace Swoft\Test\Cases;

use App\Constants\BattleType;
use App\Constants\Message;
use App\Models\BattleToken;
use Ramsey\Uuid\Uuid;
use Swoft\App;


class User31ScoreControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProvideStart
     */
    public function testStart($log,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "score/start",
                    $log
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
    public function additionProvideStart()
    {

        return [
            [[],Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideLevelUpdate
     */
    public function testLevelUpdate($win,$level,$star,$score,$expected)
    {

        $security = App::getBean('security');
        $levelToken = $security->hash($level);
        $starToken = $security->hash($star);
        $scoreToken = $security->hash($score);

        $arr = [
            'actions' => [
                '1550039646262' => [
                    "score/levelUpdate",
                    $win,
                    $levelToken,
                    $scoreToken,
                    $starToken,
                    [],
                    [],
                    []

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
    public function additionProvideLevelUpdate()
    {


        return [
            [1,1,1,10000,Message::SUCCESS],
            [1,2,1,10000,Message::SUCCESS],
            [1,3,1,10000,Message::SUCCESS],
            [1,4,1,10000,Message::SUCCESS],
            [1,5,1,10000,Message::SUCCESS],
            [2,6,1,10000,Message::SUCCESS],
            [1,4,1,10000,Message::LEVEL_REQUEST_INVALID]

        ];
    }
    /**
     * @test
     * @dataProvider additionProvideGetScores
     */
    public function testGetScores($expected)
    {



        $arr = [
            'actions' => [
                '1550039646262' => [
                    "score/getScores"
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
    public function additionProvideGetScores()
    {


        return [
            [Message::SUCCESS],
        ];
    }

}