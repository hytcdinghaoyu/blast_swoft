<?php
namespace App\Models;

use App\Base\BaseModel;
use App\Base\RedisHashModel;


class ProductBuyFlag extends RedisHashModel{
    public $uid;

    public $productId;

    public function rules()
    {
        return [
            [['uid', 'productId'], 'required'],
        ];
    }


    public static function primaryFields(){
        return [
            'uid',
            'productId'
        ];
    }
}