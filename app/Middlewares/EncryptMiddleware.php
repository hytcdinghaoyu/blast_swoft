<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2018/12/13
 * Time: 下午6:24
 */

namespace App\Middlewares;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Auth\Mapping\AuthManagerInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Server\AttributeEnum;

/**
 * @Bean()
 */
class EncryptMiddleware implements MiddlewareInterface
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
        $response = $handler->handle($request);
        $resData = $response->getAttribute(AttributeEnum::RESPONSE_ATTRIBUTE);
        $data = json_encode($this->formate($response, $resData));
        //记录action日志
        $this->logAction($request->post(), $data);
        if (!env('APP_DEBUG')) {
            $data = $this->hash($data);
        }
        $response = $response->withContent($data);
        return $response;
    }

    public function hash(string $data)
    {
        $security = App::getBean('security');
    
        if(bean(AuthManagerInterface::class)->isLoggedIn() && request()->getUri()->getPath() == '/api'){
            $security = $security->withSecret(bean(AuthManagerInterface::class)->getSession()->getExtendedData()['secretKey']);
        }
        
        $data = $security->hash($data);
        return $data;
    }

    public function formate($response, array $data)
    {
        return [
            'success' => $response->isSuccessful() ? true : false,
            'data'    => $data
        ];
    }

    /**
     * 记录action日志
     *
     * @param array  $post
     * @param string $resData
     */
    public function logAction(array $post, string $resData)
    {
        /**
         * @var $actionLogger \App\Utils\Logger
         */
        $actionLogger = App::getBean('actionLogger');
        $clientTime = $post['clientTime'];
        unset($post['clientTime']);
        $post['actions'] = [$clientTime => $post['actions']];
        $post = json_encode($post);
        $actionLogger->info("{\"request\":$post,\"response\":$resData}", ['action']);
        $actionLogger->actionLogFlush();
    }
}