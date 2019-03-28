<?php

namespace App\Base;


class RedisFlagModel extends BaseModel
{
    const FLAG_VALUE = 1;

    public $expire;

    public static function getDb()
    {
        return bean('redis');
    }

    public function primaryKey(){
        $class = get_called_class();
        return $this->formName() . ':' . implode(':', $this->getAttributes($class::primaryFields()));
    }

    public static function primaryFields(){
        return [
            'openId'
        ];
    }

    public function save(){
        $db = self::getDb();

        if(!$this->validate()){
            return false;
        }

        $primaryKey = $this->primaryKey();
        $db->SET($primaryKey, self::FLAG_VALUE);

        if($this->expire){
            $db->EXPIRE($primaryKey, $this->expire);
        }

        return true;

    }

    public function getTimeToLive(){
        $db = self::getDb();
        return $db->TTL($this->primaryKey());
    }

    public static function findOne($pkId){
        $db = self::getDb();

        $class = get_called_class();
        $model = new $class;
        if(!is_array($pkId)){
            $primaryFields = $class::primaryFields();
            $pkId = [$primaryFields[0] => $pkId];
        }
        $model->setAttributes($pkId);

        return $db->GET($model->primaryKey());

    }

}
