<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2018/12/19
 * Time: 下午5:32
 */

namespace App\Exception;

use Swoft\App;
use Swoft\Bean\Annotation\ExceptionHandler;
use Swoft\Bean\Annotation\Handler;
use Swoft\Exception\RuntimeException;
use Swoft\Auth\Exception\AuthException;
use Exception;
use InvalidArgumentException;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoft\Exception\BadMethodCallException;
use Swoft\Exception\ValidatorException;
use Swoft\Http\Server\Exception\BadRequestException;
use Swoft\Auth\Helper\ErrorCodeHelper;

/**
 * the handler of global exception
 *
 * @ExceptionHandler()
 * @uses      Handler
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AppExceptionHandler
{

    /**
     * @Handler(AuthException::class)
     *
     * @param Response   $response
     * @param \Throwable $throwable
     *
     * @return Response
     */
    public function handlerAuthException(Response $response, \Throwable $throwable)
    {

        $helper = new ErrorCodeHelper();
        $code = $throwable->getCode();
        $arr = $helper->get($code);

        $data = [
            'msg'  => $arr['message'],
            'code' => $code
        ];

        App::error(json_encode($data));

        $response = $response->withStatus($arr['statusCode']);
        return $response->json($this->format($data));
    }
    /**
     * @Handler(Exception::class)
     *
     * @param Response   $response
     * @param \Throwable $throwable
     *
     * @return Response
     */
    public function handlerException(Response $response, \Throwable $throwable)
    {
        $file = $throwable->getFile();
        $line = $throwable->getLine();
        $code = $throwable->getCode();
        $exception = $throwable->getMessage();
        $trace = $throwable->getTraceAsString();

        $data = [
            'msg'  => $exception,
            'file' => $file,
            'line' => $line,
            'code' => $code,
            'trace' => $trace
        ];
        
        $errorInfo = $this->formatError($data);
        App::error($errorInfo);
        
        unset($data['trace'], $data['file'], $data['line']);
        return $response->json($this->format($data));
    }
    


    public function format(array $data)
    {
        return [
            'success' => true,
            'data'    => $data
        ];
    }
    
    public function formatError($data){
        $str = $data['msg'] . ' in ' . $data['file'] . ':' . $data['line'];
        $str .= "\nStack trace:\n";
        $str .= $data['trace'];
        return $str;
    }
}