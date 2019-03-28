<?php
namespace App\Base;

use Monolog\Handler\AbstractProcessingHandler;

class UdpHandler extends AbstractProcessingHandler{
    
    protected $ip;
    
    protected $port;
    
    private $socket;
    
    protected function write(array $records)
    {
        if(!is_resource($this->socket)){
            $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        }
        
        if(isset($records['messages'])){
            $msg = $records['messages'] . PHP_EOL;
            socket_sendto($this->socket, $msg, strlen($msg), $flags = 0, $this->ip, $this->port);
        }
        
    }
}