<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */



class FriendsControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionGetInfo
     */

    public function getInfo($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "friends/getInfo",
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


    public function additionGetInfo()
    {
        return [
            [Message::SUCCESS]

        ];
    }

    /**
     * @test
     * @dataProvider additionRequestLives
     */

    public function requestLives($uid_arr,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "friends/requestLives",
                    $uid_arr

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


    public function additionRequestLives()
    {
        return [
            [[1],Message::SUCCESS],
        ];
    }


    /**
     * @test
     * @dataProvider additionSendLivesDirectly
     */

    public function sendLivesDirectly($uid_arr,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "friends/sendLivesDirectly",
                    $uid_arr

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


    public function additionSendLivesDirectly()
    {
        return [
            [[219445],Message::SUCCESS],
        ];
    }



    /**
     * @test
     * @dataProvider additionRequestAddFriend
     */

    public function requestAddFriend($fuidArr,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "friends/requestAddFriend",
                    $fuidArr

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


    public function additionRequestAddFriend()
    {
        return [
            [[219445],Message::SUCCESS],
        ];
    }


    /**
     * @test
     * @dataProvider additionEnsureAddFriend
     */

    public function ensureAddFriend($fuid,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "friends/ensureAddFriend",
                    $fuid

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


    public function additionEnsureAddFriend()
    {
        return [
            [219445,Message::SUCCESS],
            [1,Message::SUCCESS],
            [1,35504]
        ];
    }


    /**
     * @test
     * @dataProvider additionGetRecommendFriends
     */

    public function getRecommendFriends($expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "friends/getRecommendFriends",

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


    public function additionGetRecommendFriends()
    {
        return [
            [Message::SUCCESS],
        ];
    }



}
