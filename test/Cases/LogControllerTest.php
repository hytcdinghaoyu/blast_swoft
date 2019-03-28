<?php
namespace Swoft\Test\Cases;

use App\Constants\Message;
use Ramsey\Uuid\Uuid;

/**
 * @version   2019年2月13日
 * @author    dinghaoyu <a835399559@gmail.com>
 */



class LogControllerTest extends AbstractTestCase
{
    /**
     * @test
     * @dataProvider additionProvider
     */

    public function testLog($logtype,$log,$expected)
    {
        $arr = [
            'actions' => [
                '1550039646262' => [
                    "log/dot",
                    $logtype,
                    $log
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
        $response = $this->request('POST', '/cloud', [], parent::ACCEPT_JSON, [], $rawContent);
        $response->assertSuccessful()->assertJsonFragment(['code' => $expected]);

    }


    public function additionProvider()
    {
        return [
            ['QuestSkipFlow',json_encode(['QuestID'=>1,'DialogueSkip'=>1,]),Message::SUCCESS],
            ['QuestSkipFlow1',json_encode(['QuestID'=>1,'DialogueSkip'=>1,]),Message::SYSTEM_DATA_ERROR],
            ['AdVideoDmTickets',json_encode(['life' => 1, 'diamond' => 0, 'level' => 1]),Message::SUCCESS],
            ['AdVideoLife',json_encode(['life' => 1, 'diamond' => 0, 'level' => 1]),Message::SUCCESS],
            ['AppStart',json_encode(['StartType'=>1]),Message::SUCCESS],
            ['FailPromotionBuy',json_encode(['diamond'=>1]),Message::SUCCESS],
            ['FailPromotionInBattle',json_encode(['diamond'=>1,'level'=>1]),Message::SUCCESS],
            ['FailPromotionTap',json_encode(['diamond'=>1]),Message::SUCCESS],
            ['GuideFlow',json_encode(['iGuideId'=>1,'iLevel'=>1]),Message::SUCCESS],
            ['IcePromotionBuy',json_encode(['diamond'=>1,'paymentId'=>520,'localItems'=>1]),Message::SUCCESS],
            ['IcePromotionTap',json_encode(['diamond'=>1,'paymentId'=>520,'localItems'=>1]),Message::SUCCESS],
            ['QuestFlow',json_encode(['QuestID'=>1,'Questlevel'=>1,'QuestType'=>1,'star'=>50,'Gold'=>20,'Level'=>60,'Silver'=>1,'CountBuySkin'=>1]),Message::SUCCESS],
            ['QuestGuideFlow',json_encode(['iGuideID'=>1,'iLevel'=>1]),Message::SUCCESS],

        ];
    }
}



