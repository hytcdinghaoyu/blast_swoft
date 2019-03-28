<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2019/1/11
 * Time: 下午2:36
 */

/**
 * 定义获取用户通用参数的方法
 */
if (!function_exists('commonVar')) {
    /**
     * @return \App\Utils\CommonVar
     */
    function commonVar()
    {
        /**
         * @var $commonVar \App\Utils\CommonVar
         */
        $commonVar = \Swoft\Core\RequestContext::getContextDataByKey('commonVar');
        return $commonVar;
    }
}

if (!function_exists('authSession')) {
    function authSession()
    {
        /**
         * @var $authSession \Swoft\Auth\Bean\AuthSession
         */
        $authSession = \Swoft\Core\RequestContext::getContextDataByKey('authSession');
        return $authSession;
    }
}

if (!function_exists('globalInfo')) {
    function globalInfo()
    {
        /**
         * @var $globalInfo \App\Models\Entity\CenterUser
         */
        $globalInfo = \Swoft\Core\RequestContext::getContextDataByKey('globalInfo');
        return $globalInfo;
    }
}