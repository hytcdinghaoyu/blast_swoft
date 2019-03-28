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
use Swoft\Auth\Middleware\AuthMiddleware;
use Swoft\Bean\Annotation\Bean;
use Swoft\Core\RequestHandler;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Server\Exception\BadRequestException;

/**
 * @Bean()
 */
class ModulesDispatchMiddleware implements MiddlewareInterface
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
        $uri = $request->getUri()->getPath();

        if(!in_array($uri, ['/api', '/cloud'])){
            throw new BadRequestException('Module not found!');
        }

        //game server需要鉴权
        if($uri == '/api'  && $handler instanceof RequestHandler){
            return bean(AuthMiddleware::class)->process($request, $handler);
        }

        return $handler->handle($request);
    }

}