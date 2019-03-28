<?php

namespace App\Utils;

class Security
{
    private $macHash = 'sha256';
    private $secret = '05777b28db2723633c5648d6576555a4';
    private $iv = '3c5648d6576555a4';

    public function withSecret($secret = '')
    {
        $clone = clone $this;
        $clone->secret = $secret;
        return $clone;
    }

    public function hash($data)
    {
        $data = $this->encrypt($data, 'base64');
        $hashed = hash_hmac($this->macHash, $data, $this->secret);
        $hashed = strtoupper($hashed);
        return $hashed . $data;
    }

    public function unHash($data)
    {
        $hash = substr($data, 0, 64);
        $reData = substr($data, 64);
        $newHash = hash_hmac($this->macHash, $reData, $this->secret);
        if ($hash !== strtoupper($newHash)) {
            return false;
        }
        return $this->decrypt($reData, 'base64');
    }


    public function encrypt($str)
    {
        return trim(openssl_encrypt(trim($str), 'aes-256-cbc', $this->secret, 0, $this->iv));
    }

    public function decrypt($str)
    {
        $ret = openssl_decrypt(base64_decode($str), 'aes-256-cbc', $this->secret,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->iv);
        if (strripos($ret, '}') == false) {//api敏感接口数据加密，没有括号
            return $ret;
        }
        return substr($ret, 0, strripos($ret, '}') + 1);
    }
}