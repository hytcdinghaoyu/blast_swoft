<?php

namespace Swoft\Test\Cases;

use App\Constants\Message;
use Ramsey\Uuid\Uuid;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */
class LeaderboardControllerTest extends AbstractTestCase
{

    /**
     * @test
     * @dataProvider additionProviderSubmit
     */
    public function testSubmit($leaderBoard, $score, $expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "leaderboard/submit",
                    $leaderBoard,
                    $score
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
        $response = $this->request('POST', '/cloud', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderSubmit()
    {

        return [
            ['daily', 10, Message::SUCCESS],
            ['weekly', 100, Message::SUCCESS],
            ['daily1', 12, Message::LEADERBOARD_NOT_FOUND]

        ];
    }


    /**
     * @test
     * @dataProvider additionProviderGet
     */
    public function testGet($leaderBoard, $expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "leaderboard/get",
                    $leaderBoard,
                    [$GLOBALS['uid']]
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
        $response = $this->request('POST', '/cloud', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderGet()
    {
        return [
            ['daily', Message::SUCCESS],
            ['weekly', Message::SUCCESS],
            ['weekly1', Message::LEADERBOARD_NOT_FOUND]
        ];
    }


    /**
     * @test
     * @dataProvider additionProviderList
     */
    public function testList($leaderBoard, $amount, $offset, $expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "leaderboard/list",
                    $leaderBoard,
                    $amount,
                    $offset
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
        $response = $this->request('POST', '/cloud', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);
    }

    public function additionProviderList()
    {

        return [
            ['daily', 1, 0, Message::SUCCESS],
            ['weekly', 1, 0, Message::SUCCESS],
            ['weekly1', 1, 0, Message::LEADERBOARD_NOT_FOUND]
        ];
    }


}