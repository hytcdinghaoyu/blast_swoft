<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2018/12/13
 * Time: 下午3:00
 */

namespace App\Middlewares;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Auth\Mapping\AuthManagerInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\JsonHelper;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Server\Exception\BadRequestException;

/**
 * @Bean()
 */
class DecryptMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() != 'POST') {
            throw new BadRequestException('wrong request method!');
        }
        
        $contents = $request->raw();
        if (!env('APP_DEBUG')) {
            $contents = $this->getRequestInfo($contents);
        }
        $parsedBody = JsonHelper::decode($contents, true);
        $request = $request->withParsedBody($parsedBody);
        return $handler->handle($request);
    }

    public function getRequestInfo($contents)
    {
        $security = App::getBean('security');
        
        if(bean(AuthManagerInterface::class)->isLoggedIn()){
            $security = $security->withSecret(bean(AuthManagerInterface::class)->getSession()->getExtendedData()['secretKey']);
        }
        
        $data = $security->unHash($contents);
        return $data;
    }
}