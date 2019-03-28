<?php

namespace App\Models;


use App\Base\RedisHashModel;

class TencentAccess extends RedisHashModel
{
    public $openId;
    
    public $openKey = '';
    
    public $pf = '';
    
    public $pfKey = '';
    
    public function rules()
    {
        return [
            [['openId', 'openKey', 'pf', 'pfKey'], 'required'],
        ];
    }
    
    
    public static function primaryFields(){
        return [
            'openId'
        ];
    }
    
}
