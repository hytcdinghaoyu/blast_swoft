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

use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Auth\Constants\AuthConstants;
use Swoft\Auth\Exception\AuthException;
use Swoft\Auth\Helper\ErrorCode;
use Swoft\Auth\Mapping\AuthManagerInterface;
use Swoft\Auth\Mapping\AuthorizationParserInterface;

class TokenHeaderParser implements AuthorizationParserInterface
{

    /**
     * @throws AuthException When AuthHandler missing or error.
     */
    public function parse(ServerRequestInterface $request): ServerRequestInterface
    {
        $token = $request->getHeaderLine('token');
        if(!$token){
            throw new AuthException(ErrorCode::AUTH_TOKEN_INVALID, 'Token not provided');
        }

        $manager = App::getBean(AuthManagerInterface::class);
        if (!$manager instanceof AuthManagerInterface) {
            throw new AuthException(ErrorCode::POST_DATA_NOT_PROVIDED, 'AuthorizationParser should implement Swoft\Auth\Mapping\AuthorizationParserInterface');
        }

        $res = $manager->authenticateToken($token);
        $request = $request->withAttribute(AuthConstants::IS_LOGIN, $res);
        return $request;
    }

}
