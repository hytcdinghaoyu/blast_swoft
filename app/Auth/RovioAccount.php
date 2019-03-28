<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Auth;

use App\Models\Dao\CenterUserDao;
use App\Models\Entity\CenterUser;
use Swoft\Auth\Bean\AuthResult;
use Swoft\Auth\Mapping\AccountTypeInterface;
use Swoft\Bean\Annotation\Bean;

/**
 * Class RovioAccount
 * @Bean()
 */
class RovioAccount implements AccountTypeInterface
{
    /**
     * @param array $data Login data
     *
     * @return AuthResult|null
     */
    public function login(array $data): AuthResult
    {
        $result = new AuthResult();
        if (isset($data['thirdId'])) {
            $result->setIdentity($data['thirdId']);
            $result->setExtendedData($data);
        }
        return $result;
    }

    /**
     * @param string $identity Identity
     *
     * @return bool Authentication successful
     */
    public function authenticate(string $identity): bool
    {
        $user = \bean(CenterUserDao::class)->findOneByThirdIdFromCache($identity);
        if(!$user){
            return false;
        }
        \Swoft\Core\RequestContext::setContextDataByKey('globalInfo', $user);
        return true;
    }
}
