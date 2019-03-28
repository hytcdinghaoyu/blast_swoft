<?php
namespace Swoft\Test\Cases;

use App\Constants\BattleType;
use App\Constants\Message;
use App\Models\BattleToken;
use Ramsey\Uuid\Uuid;
use Swoft\App;


class User3DailyMissionControllerTest extends AbstractTestCase
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
                    "dailyMission/init",
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
            [Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideStart
     */
    public function testStart($log,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "dailyMission/start",
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
    public function testLevelUpdate($pigNum,$expected)
    {
        echo "update";
        $mineToken = BattleToken::findOne(['uid' => $GLOBALS['uid'], 'battleType' => BattleType::DAILY_MISSION]);
        $securityTmp = App::getBean('security');
        $security = $securityTmp->withSecret($mineToken->secret);
        $pigToken = $security->hash($pigNum);
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "dailyMission/levelUpdate",
                    [],
                    $pigToken,
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
            [100,Message::SUCCESS]
        ];
    }

    /**
     * @test
     * @dataProvider additionProvideBuy
     */
    public function testBuy($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "dailyMission/buy"

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
    public function additionProvideBuy()
    {

        return [
            [Message::SUCCESS]
        ];
    }
}