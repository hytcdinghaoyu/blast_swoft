<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;
use Ramsey\Uuid\Uuid;


class UserControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProvideCreate
     */
    public function testCreate($goldCoin,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/create",
                    $goldCoin
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
    public function additionProvideCreate()
    {

        return [
            [200,Message::SUCCESS],
            [200,Message::USER_INFO_EXISTED]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideInitialize
     */
    public function testInitialize($startFromPlatform,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/initialize",
                    $startFromPlatform
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
    public function additionProvideInitialize()
    {

        return [
            [1,Message::SUCCESS],
            [0,Message::SUCCESS]
        ];
    }

    /**
     * @test
     * @dataProvider additionProvideGetInfo
     */
    public function testGetInfo($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/getInfo"
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
    public function additionProvideGetInfo()
    {

        return [
            [Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideGetAllFriends
     */
    public function testGetAllFriends($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/getAllFriends"
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
    public function additionProvideGetAllFriends()
    {

        return [
            [Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideUpdate
     */
    public function testUpdate($userInfo,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/update",
                    $userInfo
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
    public function additionProvideUpdate()
    {

        return [
            [['username'=>'ROBOT'],Message::SUCCESS]
        ];
    }

    /**
     * @test
     * @dataProvider additionProvideGetInfoByOpenId
     */
    public function testGetInfoByOpenId($openIds,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/getInfoByOpenId",
                    $openIds
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
    public function additionProvideGetInfoByOpenId()
    {

        return [
            [[],Message::USER_OPENID_CANNOT_EMPTY],
            [['A'],Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideOnline
     */
    public function testOnline($topLevel,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/online",
                    $topLevel
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
    public function additionProvideOnline()
    {

        return [
            [1,Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideEntiData
     */
    public function testEntiData($str,$expected)
    {
        echo 1;
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/entiData",
                    $str
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
    public function additionProvideEntiData()
    {

        return [
            ['test',Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideGetInfoByUid
     */
    public function testGetInfoByUid($uidArr,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/getInfoByUid",
                    $uidArr
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
    public function additionProvideGetInfoByUid()
    {

        return [
            [[27,28],Message::SUCCESS]
        ];
    }
    /**
     * @test
     * @dataProvider additionProvideFriendsRankingList
     */
    public function testFriendsRankingList($uidArr,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "user/friendsRankingList",
                    $uidArr
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
    public function additionProvideFriendsRankingList()
    {

        return [
            [[27,28],Message::SUCCESS]
        ];
    }
}