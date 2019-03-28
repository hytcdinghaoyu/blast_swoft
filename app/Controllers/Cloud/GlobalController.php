<?php
/**
 * This file is part of Swoft.
 *
 * @link    https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Controllers\Cloud;

use App\Auth\RovioAccount;
use App\Constants\Message;
use App\Constants\OSPlatform;
use App\Constants\ThirdPlatform;
use App\Controllers\CommonController;
use App\Models\Dao\CenterUserDao;
use App\Models\Dao\UserInfoDao;
use App\Models\TencentAccess;
use App\Services\PunishService;
use App\Services\ReportDataService;
use App\Utils\Utils;
use App\Models\Entity\CenterUser;
use Swoft\App;
use Swoft\Auth\Mapping\AuthManagerInterface;
use Swoft\Core\RequestContext;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;
use Ramsey\Uuid\Uuid;
use Swoft\Bean\Annotation\Inject;

/**
 * global注册服务.
 *
 * @Controller(prefix="/global")
 */
class GlobalController extends CommonController
{

    /**
     * @Inject()
     * @var CenterUserDao
     */
    private $centerUserDao;


    /**
     * 注册登录接口
     * @RequestMapping(route="roviologin", method={RequestMethod::POST})
     *
     * @param string $uuid
     * @param int    $customerId
     *
     * @return array
     */
    public function RovioLogin(string $uuid, int $customerId = 0)
    {
        $user = $this->centerUserDao->findOneByThirdIdFromCache($uuid);
        $isCreate = false;
        if (!$user) {  //不存在创建新用户
            $user = $this->centerUserDao->createRovioPlayer($uuid, $customerId);
            $isCreate = true;
        } else {  //已注册更新key
            $user = $this->centerUserDao->updateSecret($user);
        }

        $session = bean(AuthManagerInterface::class)->login(RovioAccount::class, $user->toArray());
        return $this->returnData([
            'uid'         => $user->getUid(),
            'uuid'        => $user->getUuid(),
            'server_name' => 'guest',
            'server_url'  => $this->serverUrl('guest'),
            'key'         => $user->getSecretKey(),
            'token'       => $session->getToken(),
            'create'      => (int)$isCreate
        ]);
    }

    /**
     * @RequestMapping(route="login", method={RequestMethod::POST})
     *
     * @param         $openId
     * @param string  $thirdFrom
     * @param string  $openKey
     * @param string  $pf
     * @param string  $pfKey
     *
     * @return array
     * @throws \Exception
     */
    public function login($openId, $thirdFrom = 'guest', $openKey = '', $pf = '', $pfKey = '')
    {
        $commonVar = commonVar();

        if (!ThirdPlatform::isValidName($thirdFrom) || !OSPlatform::isValidName($commonVar->getPlatform())) {
            return $this->returnError(Message::THIRD_OR_PLATFORM_NOT_EXIST);
        }

        $serverName = ($thirdFrom == 'guest') ? 'guest' : $thirdFrom . '_' . $commonVar->getPlatform();

//        if($serverName != \Yii::$app->params['zone']){
//            return [
//                "code" => 31001,
//                "msg" => "Zone error! Server zone: ".\Yii::$app->params['zone'].", Client zone: $serverName"
//            ];
//        }

        $user = $this->centerUserDao->findOneByThirdIdFromCache($openId);
        $isCreate = false;

        //判断用户是否封号
        $banUserRes = PunishService::LoginPunishInfo($user, $openId);
        if (!empty($banUserRes)) {
            return $this->returnError(Message::BAN_USER_LOGIN, $banUserRes['banReason']);
        }

        if (!$user) {//不存在创建新用户
            $user = $this->centerUserDao->createTencentPlayer($openId, $serverName, $thirdFrom);
            $isCreate = true;
        } else {
            $user = $this->centerUserDao->updateSecret($user);
        }
        
        $userInfo = bean(UserInfoDao::class)->findOneByUid($user->getUid());
        if(!$userInfo){
            $isCreate = true;
        }


        //腾讯登录态，存redis hash结构
        $access = new TencentAccess();
        $access->openId = $openId;
        $access->openKey = $openKey;
        $access->pf = $pf;
        $access->pfKey = $pfKey;
        $access->save();
        if (!$user) {
//            \Yii::error(json_encode($user->getErrors()));
            return $this->returnError(Message::CREATE_USER_FAIL);
        }

        ReportDataService::ReportLoginInfo($isCreate, $openId,$user);


        $session = bean(AuthManagerInterface::class)->login(RovioAccount::class, $user->toArray());

        return $this->returnData([
            'uid'         => $user->getUid(),
            'uuid'        => $user->getUuid(),
            'server_name' => $serverName,
            'server_url'  => $this->serverUrl($serverName),
            'key'         => $user->getSecretKey(),
            'token'       => $session->getToken(),
            'create'      => (int)$isCreate
        ]);
    }

    /**
     * 根据zone返回分区服务器地址
     *
     * @param $serverName
     *
     * @return mixed
     */
    protected function serverUrl($serverName)
    {
        $servers = config('servers');
        return isset($servers [$serverName]) ? $servers [$serverName] : $servers ['guest'];
    }
}
