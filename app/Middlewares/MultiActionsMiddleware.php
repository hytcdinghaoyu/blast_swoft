<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2018/12/13
 * Time: 下午5:55
 */

namespace App\Middlewares;

use App\Constants\OSPlatform;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Core\RequestContext;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Server\AttributeEnum;
use \Swoft\Http\Server\Exception\BadRequestException;

/**
 * @Bean()
 */
class MultiActionsMiddleware implements MiddlewareInterface
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
        $post = $request->getParsedBody();
        //设置CommonVar
        RequestContext::setContextDataByKey('commonVar', $this->getCommonVar($post));

        $response = null;
        foreach ($post['actions'] as $clientTime => $action) {
            $action[0] = strtolower($action[0]);
            $httpRouter = App::getBean('httpRouter');
            $httpHandler = $httpRouter->getHandler('/' . array_shift($action), 'POST');

            //Router Exception
            if ($httpHandler[2] === null) {
                throw new BadRequestException($action[0].'Action not found!');
            }

            //绑定POST参数,路由参数
            $matches = App::getBean('reflectionUserParams')->getFuncParams($httpHandler[2]['handler'], $action);
            $httpHandler[2]['matches'] = $matches;
            $requestSub = $request->withAttribute(AttributeEnum::ROUTER_ATTRIBUTE, $httpHandler);

            //重新设置POST数据
            $requestSub = $requestSub->withParsedBody(
                array_merge($post, ['actions' => $post['actions'][$clientTime]], ['clientTime' => floor($clientTime / 10000)])
            );
            RequestContext::setRequest($requestSub);

            $response = $handler->handle($requestSub);
        }
        return $response;
    }

    public function getCommonVar($data)
    {
        $attrs = [
            'platform'      => OSPlatform::valueToLowerStr($data['platform'] ?? OSPlatform::IOS),
            'bindPlatform'  => strtolower($data['bindPlatform'] ?? 'guest'),
            'openId'        => $data['openId'] ?? '',
            'appVersion'    => $data['appVersion'] ?? 1,
            'deviceId'      => $data['deviceId'] ?? '',
            'channel'       => $data['channel'] ?? 'unknown',
            'configVersion' => $data['configVersion'] ?? '1.0.0',
            'uid'           => $data['uid'] ?? 0,
            'uuid'          => $data['uuid'] ?? "",
        ];
        return App::getBean('commonVar')->withAttributes($attrs);
    }
}