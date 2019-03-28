<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Models\Dao;

use App\Constants\ThirdPlatform;
use App\Models\Entity\CenterUser;
use App\Utils\Utils;
use Ramsey\Uuid\Uuid;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Db\Query;
use Swoftx\Aop\Cacheable\Annotation\Cacheable;

/**
 *
 * @Bean()
 * @uses      CenterUserDao
 */
class CenterUserDao
{
    /**
     * @Cacheable(key="user_third_id_cache:{0}", ttl=604800)
     */
    public function findOneByThirdIdFromCache($thirdId)
    {
        $record = CenterUser::findOne(['thirdId' => $thirdId])->getResult();
        return $record;
    }
    
    /**
     * @Cacheable(key="user_uid_id_cache:{0}", ttl=604800)
     */
    public function findOneByUidFromCache($uid)
    {
        $record = CenterUser::findOne(['uid' => $uid])->getResult();
        return $record;
    }

    /**
     * @param $rovioUuid
     * @param $customerId
     * @return CenterUser
     */
    public function createRovioPlayer($rovioUuid, $customerId){
        $commonVar = commonVar();

        $user = new CenterUser();
        $user->setUid(Utils::createUid());
        $user->setUuid($rovioUuid);
        $user->setThirdId($rovioUuid);
        $user->setCustomerId($customerId);
        $user->setZone('guest');
        $user->setPlatform($commonVar->getPlatform());
        $user->setSecretKey(bean('yiiSecurity')->generateRandomString(32));
        $user->setChannel($commonVar->getChannel());
        $user->setDeviceId($commonVar->getDeviceId());
        $user->setCreatedAt(time());
        $user->setUpdatedAt(time());
        $user->save()->getResult();

        return $user;
    }

    /**
     * @param $openId
     * @param $serverName
     * @param $thirdFrom
     * @return CenterUser
     * @throws \Exception
     */
    public function createTencentPlayer($openId, $serverName, $thirdFrom){
        $commonVar = commonVar();

        $user = new CenterUser();
        $user->setUid(Utils::createUid());
        $user->setUuid(Uuid::uuid5(Uuid::NAMESPACE_DNS, Uuid::uuid1() . Uuid::uuid4()));
        $user->setZone($serverName);
        $user->setThirdId($openId);
        $user->setPlatform($commonVar->getPlatform());
        $user->setThirdBind(ThirdPlatform::getValueByName($thirdFrom));
        $user->setChannel($commonVar->getChannel());
        $user->setDeviceId($commonVar->getDeviceId());
        $user->setRegisterAppVersion(Utils::appVersionToInt($commonVar->getAppVersion()));
        $user->setCreatedAt(time());
        $user->setUpdatedAt(time());
        $user->setSecretKey(bean('yiiSecurity')->generateRandomString(32));
        $user->save()->getResult();

        return $user;
    }

    public function updateSecret(CenterUser $centerUser){
        $centerUser->setSecretKey(bean('yiiSecurity')->generateRandomString(32));
        $centerUser->setUpdatedAt(time());
        $centerUser->update()->getResult();
        return $centerUser;
    }





    public static function findOneByUUID($uuid)
    {
        return Utils::formatArrayValue(Query::table(CenterUser::class)->condition(['uuid' => $uuid])->one()->getResult());

    }

    /**
     * 获取中心用户的数据，数据缓存至redis
     * @param $uuid
     * @return array|bool
     */
    public static function findCenterUserByUid($uid)
    {
        return Utils::formatArrayValue(Query::table(CenterUser::class)->condition(['uid' => $uid])->one()->getResult());
    }

    public static function findCenterUserByOpenId($openId)
    {
        return Utils::formatArrayValue(Query::table(CenterUser::class)->condition(['thirdId' => $openId])->one()->getResult());
    }


    public static function uuidArrToUidArr($uuidArr){
        $uidArr = Utils::formatArrayValue(Query::table(CenterUser::class)->condition(['uuid' => $uuidArr])->get()->getResult());
        $resArr=[];
        foreach ($uidArr as $key=>$value) {
            $resArr[$value['uuid']]=$value['uid'];
        }
        return $resArr;
    }
}
