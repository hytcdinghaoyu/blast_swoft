<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */



class User4FamilyControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProviderGetInfo
     */

    public function getInfo($uid,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "family/getInfo",
                    $uid

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


    public function additionProviderGetInfo()
    {
        return [
            [27,Message::SUCCESS]

        ];
    }
    /**
     * @test
     * @dataProvider additionProviderUpdate
     */

    public function update($fields,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "family/update",
                    $fields

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


    public function additionProviderUpdate()
    {
        return [
            [['quests'=>[
                ['uid'=>27,'questId'=>'ch1_build_sea_restaurant2','draw'=>0,'state'=>1,'updated_at'=>time()]
            ]], Message::SUCCESS]

        ];
    }

    /**
     * @test
     * @dataProvider additionProviderReward
     */

    public function reward($rewardId,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "family/reward",
                    $rewardId

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
            ['new_day_04',Message::SUCCESS]

        ];
    }




    /**
     * @test
     * @dataProvider additionProviderGetReward
     */

    public function getReward($id,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "family/getReward",
                    $id

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


    public function additionProviderGetReward()
    {
        return [
            [1,Message::SUCCESS]

        ];
    }


    /**
     * @test
     * @dataProvider additionProviderChapterRewarded
     */

    public function chapterRewarded($chapterId,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "family/getReward",
                    $chapterId

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
        $response = $this->request('POST', '/api', [], parent::ACCEPT_JSON,  ['token'=>$GLOBALS['token']], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);

    }


    public function additionProviderChapterRewarded()
    {
        return [
            [1,Message::SUCCESS]

        ];
    }
}
