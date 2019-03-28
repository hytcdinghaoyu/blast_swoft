<?php
namespace App\Services;
use Swoft\Bean\Annotation\Bean;


/**
 * @Bean(ref="rovio")
 * Interface PayServiceInterface
 * @package App\Services
 */
interface PayServiceInterface{
    /**
     * 获取余额
     * @return mixed
     */
    public function getBalance() : int;
    
    /**
     * 付钱
     * @return mixed
     */
    public function payMoney($amount) : bool;
    
    /**
     * 送钱
     * @return mixed
     */
    public function presentMoney($amount) : bool;
    
    
}
