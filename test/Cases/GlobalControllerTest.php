<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;
use App\Models\Entity\UserInfo;
use Ramsey\Uuid\Uuid;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */
class GlobalControllerTest extends AbstractTestCase
{

    /**
     * @test
     * @dataProvider additionProvider
     */
    public function testLogin($uuid, $thirdFrom, $expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "global/login",
                    $uuid,
                    $thirdFrom
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/cloud', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);

        $result = json_decode($response->getBody()->getContents(), true);
        if(isset($result['data']['uuid']) && !isset($GLOBALS['uuid'])){
            $GLOBALS['uuid'] = $result['data']['uuid'];
        }
        if(isset($result['data']['token']) && !isset($GLOBALS['token'])){
            $GLOBALS['token'] = $result['data']['token'];
        }
        if(isset($result['data']['key']) && !isset($GLOBALS['key'])){
            $GLOBALS['key'] = $result['data']['key'];
        }
        if(isset($result['data']['uid']) && !isset($GLOBALS['uid'])){
            $GLOBALS['uid'] = $result['data']['uid'];
//            $userInfo = new UserInfo();
//            $userInfo->setUid($result['data']['uid']);
//            $userInfo->setUsername('testUser');
//            $userInfo->setCreatedAt(time());
//            $userInfo->setUpdatedAt(time());
//            $userInfo->setSilverCoin(100);
//            $userInfo->setGoldCoin(100);
//            $userInfo->setLives(5);
//            $userInfo->save()->getResult();
        }

    }

    public function additionProvider()
    {
        $uuid1 = Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4());
        $uuid2 = Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4());
        $uuid3 = Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4());
        return [
            [$uuid1, 'qq', Message::SUCCESS],
            [$uuid2, 'wx', Message::SUCCESS],
            [$uuid3, 'guest', Message::SUCCESS],
            [$uuid3, 'wrong_platform', Message::THIRD_OR_PLATFORM_NOT_EXIST],
        ];
    }

    /**
     * @test
     * @dataProvider additionProviderForRovio
     */
    public function testRovioLogin($uuid, $customerId)
    {

        $arr = [
            'actions' => [
                '1550039646262' => [
                    "global/rovioLogin",
                    $uuid,
                    $customerId
                ]
            ],
            "appVersion" => $GLOBALS['appVersion'],
            "channel" => $GLOBALS['channel'],
            "configVersion" => $GLOBALS['configVersion'],
            "deviceId" => $GLOBALS['deviceId'],
            "platform" => $GLOBALS['platform']
        ];
        $rawContent = json_encode($arr);
        $response = $this->request('POST', '/cloud', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => 1]);
    }

    public function additionProviderForRovio()
    {
        $uuid1 = Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4());
        $uuid2 = Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4());
        $uuid3 = Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4());
        return [
            [$uuid1, crc32($uuid1)],
            [$uuid1, crc32($uuid1)],
            [$uuid2, crc32($uuid2)],
            [$uuid2, crc32($uuid2)],
            [$uuid3, crc32($uuid3)],
            [$uuid3, crc32($uuid3)],
        ];
    }



}