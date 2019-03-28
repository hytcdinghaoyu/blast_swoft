<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;
use Ramsey\Uuid\Uuid;


class BulletinBoardControllerTest extends AbstractTestCase
{


    /**
     * @test
     * @dataProvider additionProvideList
     */
    public function testList($language,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "bulletinBoard/list",
                    $language
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
    public function additionProvideList()
    {

        return [
            ['zh',Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideGet
     */
    public function testGet($ids,$language,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "bulletinBoard/get",
                    $ids,
                    $language
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
    public function additionProvideGet()
    {

        return [
            [[192],'zh',Message::SUCCESS]
        ];
    }
}