<?php

namespace App\Controllers;

use App\Constants\Message;

class CommonController
{

    public function returnSuccess()
    {
        return [
            'code' => Message::SUCCESS,
            'msg'  => 'action succeed!'
        ];
    }

    public function returnError($code = 0, $error_info = '')
    {
        $msg = $error_info ? Message::getMessage($code) . '|' . $error_info : Message::getMessage($code);
        return [
            'code' => $code,
            'msg'  => $msg,
        ];
    }

    public function returnData($data = [])
    {
        return array_merge(['code' => Message::SUCCESS], $data);
    }

}
