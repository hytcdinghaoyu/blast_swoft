<?php
namespace App\Base;


class RedisHashModel extends BaseModel{

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
        $db->hMset($primaryKey, $this->getAttributes());

        if(isset($this->expire)){
            $db->EXPIRE($primaryKey, $this->expire);
        }

        return true;

    }

    public function getTimeToLive(){
        $db = self::getDb();
        return $db->TTL($this->primaryKey());
    }

    public function delete(){
        $db = self::getDb();
        return $db->DEL($this->primaryKey());
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

        $fields = $db->hGetAll($model->primaryKey());

        if(!$fields){
            return null;
        }

        $model->setAttributes($fields);
        return $model;
    }
}