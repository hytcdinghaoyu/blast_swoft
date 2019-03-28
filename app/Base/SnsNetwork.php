<?php
namespace App\Base;

use Swoft\App;
use Swoole\Coroutine\Http\Client;

/**
 * 发送HTTP网络请求类
 *
 * @version 3.0.0
 * @author open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:	 
 *				 3.0.1 | coolinchen | 2012-09-07 10:30:00 | add funtion makeRequestWithFile
 *               3.0.0 | nemozhang | 2011-03-09 15:33:04 | initialization				 
 */


class SnsNetwork
{
	/**
	 * 执行一个 HTTP 请求
	 *
	 * @param string 	$url 	执行请求的URL 
	 * @param mixed	$params 表单参数
	 * 							可以是array, 也可以是经过url编码之后的string
	 * @param mixed	$cookie cookie参数
	 * 							可以是array, 也可以是经过拼接的string
	 * @param string	$method 请求方法 post / get
	 * @param string	$protocol http协议类型 http / https
	 * @return array 结果数组
	 */
	static public function makeRequest($url, $params, $cookie, $method='post', $protocol='http')
	{   
		$query_string = self::makeQueryString($params);	   
	    $cookie_string = self::makeCookieString($cookie);
	    	    
	    $ch = curl_init();

	    if ('GET' == strtoupper($method))
	    {
		    curl_setopt($ch, CURLOPT_URL, "$url?$query_string");
	    }
	    else 
        {
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
	    }
        
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

	    if (!empty($cookie_string))
	    {
	    	curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
	    }
	    
	    if ('https' == $protocol)
	    {
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    }
	
	    $ret = curl_exec($ch);
	    $err = curl_error($ch);
	    
	    if (false === $ret || !empty($err))
	    {
		    $errno = curl_errno($ch);
		    $info = curl_getinfo($ch);
		    curl_close($ch);

	        return array(
	        	'result' => false,
	        	'errno' => $errno,
	            'msg' => $err,
	        	'info' => $info,
	        );
	    }
	    
       	curl_close($ch);

        return array(
        	'result' => true,
            'msg' => $ret,
        );
	            
	}
    
    /**
     * 协程版本请求
     * @param        $url
     * @param        $params
     * @param        $cookie
     * @param string $method
     * @param string $protocol
     *
     * @return array
     */
    static public function makeCoRequest($url, $path, $params, $cookie = [], $method='post', $protocol='http', $port = 80)
    {
        $query_string = self::makeQueryString($params);
        
        $ssl = $protocol === 'https';
        if($protocol === 'https'){
            $port = 443;
        }
        $client = new Client($url, $port, $ssl);
        $client->set([ 'timeout' => 3]);
        
        if (!empty($cookie))
        {
            $client->setCookies($cookie);
        }
    
        $client->setDefer();
        if ('GET' == strtoupper($method)){
            $client->get("$path?$query_string");
        }else{
            $client->post($path, $params);
        }
    
        
        $client->recv();
        $ret = $client->body;
    
        if (null !== $client->errCode && $client->errCode !== 0) {
            App::error(sprintf('HttpClient Request ERROR #%s url=%s', $client->errCode, $url));
            $client->close();
            return array(
                'result' => false,
                'errno' => $client->errCode,
                'msg' => $client->errMsg,
                'info' => $client->errMsg,
            );
//            throw new \RuntimeException(\socket_strerror($client->errCode), $client->errCode);
        }
    
        $client->close();
        
        return array(
            'result' => true,
            'msg' => $ret,
        );
        
    }
	
	
	/**
	 * 执行一个 HTTP 请求,以post方式，multipart/form-data的编码类型上传文件
	 *
	 * @param string 	$url 	执行请求的URL
	 * @param mixed	$params 表单参数，必须是array, 对于文件表单项 直接传递文件的全路径, 并在前面增加'@'符号
     *                          举例: array('upload_file'=>'@/home/xxx/hello.jpg', 'field1'=>'value1');
	 * @param mixed	$cookie cookie参数
	 * 							可以是array, 也可以是经过拼接的string
	 * @param string	$protocol http协议类型 http / https
	 * @return array 结果数组
	 */
	static public function makeRequestWithFile($url, $params, $cookie, $protocol='http')
	{      
	    $cookie_string = self::makeCookieString($cookie);
	    	    
	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

	    if (!empty($cookie_string))
	    {
	    	curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
	    }
	    
	    if ('https' == $protocol)
	    {
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    }
	
	    $ret = curl_exec($ch);
	    $err = curl_error($ch);
	    
	    if (false === $ret || !empty($err))
	    {
		    $errno = curl_errno($ch);
		    $info = curl_getinfo($ch);
		    curl_close($ch);

	        return array(
	        	'result' => false,
	        	'errno' => $errno,
	            'msg' => $err,
	        	'info' => $info,
	        );
	    }
	    
       	curl_close($ch);

        return array(
        	'result' => true,
            'msg' => $ret,
        );
	            
	}
	
	
	static public function makeQueryString($params)
	{
		if (is_string($params))
			return $params;
			
		$query_string = array();
	    foreach ($params as $key => $value)
	    {   
	        array_push($query_string, rawurlencode($key) . '=' . rawurlencode($value));
	    }   
	    $query_string = join('&', $query_string);
	    return $query_string;
	}

	static public function makeCookieString($params)
	{
		if (is_string($params))
			return $params;
			
		$cookie_string = array();
	    foreach ($params as $key => $value)
	    {   
	        array_push($cookie_string, $key . '=' . $value);
	    }   
	    $cookie_string = join('; ', $cookie_string);
	    return $cookie_string;
	}	
}

// end of script
