<?php

namespace App\Constants;

/**
 * 错误码类
 *
 * @author dhy
 */
class MsdkMessage
{
    const SUCCESS = 0;
    const BUSY = 1;
    const TOKEN_EXPIRED = 2;
    const TOKEN_NOT_EXIST = 3;
    const PARAM_ERROR = 4;
    const ORDER_EXISTS = 5;
    
    //系统超时导致无法确认订单状态
    const ORDER_NOT_CONFIRMED = 3000111;
    //上次操作成功，可以发货
    const LAST_SUCCESS = 1002215;
    
    
    /**
     * 获取错误码对应的翻译
     */
    public static function getMessage($code)
    {
        return isset(static::messagePacket()[$code]) ? static::messagePacket()[$code] : 'msg not found';
    }
    
    /**
     * 错误码翻译包
     */
    private static function messagePacket()
    {
        return [
            static::TOKEN_EXPIRED => 'token expired',
            static::TOKEN_NOT_EXIST => 'token not exists',
            static::PARAM_ERROR => 'param error',
            static::ORDER_EXISTS => 'order exists',
        
        ];
    }
}