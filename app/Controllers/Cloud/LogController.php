<?php

namespace App\Controllers\Cloud;


use App\Constants\Message;
use App\Controllers\CommonController;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;


/**
 * 日志服务.
 *
 * @Controller(prefix="/log")
 */
class LogController extends CommonController
{

    /**
     * 客户端日志打点
     * @RequestMapping(route="dot", method={RequestMethod::POST})
     *
     * @param string $log_type
     * @param array  $client_log
     *
     * @return array
     */
    public function actionDot($log_type = '', $client_log = [])
    {

        $class = 'App\\datalog\\dot\\' . ucfirst($log_type);

        if (!class_exists($class)) {
            return $this->returnError(Message::SYSTEM_DATA_ERROR, "dot:logtype: {$log_type} invalid");
        }

        $logObj = new $class();
        $logObj->mergeClientLog($client_log);
        $logObj->send();

        return $this->returnSuccess();
    }


}
