<?php

namespace App\Base;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Scope;
use yii\base\InvalidConfigException;


class BaseModel
{

    const TYPE_REQUIRED = 'required';

    const TYPE_INTEGER = 'integer';

    const TYPE_STRING = 'string';

    const TYPE_IN = 'in';

    /**
     * Returns the list of attribute names.
     * By default, this method returns all public non-static properties of the class.
     * You may override this method to change the default behavior.
     * @return array list of attribute names.
     */
    public function attributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties() as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }

    /**
     * Returns attribute values.
     * @param array $names list of attributes whose value needs to be returned.
     * Defaults to null, meaning all attributes listed in [[attributes()]] will be returned.
     * If it is an array, only the attributes in the array will be returned.
     * @param array $except list of attributes whose value should NOT be returned.
     * @return array attribute values (name => value).
     */
    public function getAttributes($names = null, $except = [])
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * Sets the attribute values in a massive way.
     * @param array $values attribute values (name => value) to be assigned to the model.
     */
    public function setAttributes($values)
    {
        if (is_array($values)) {
            $attributes = array_flip($this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                }
            }
        }
        return $this;
    }

    /**
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return \json_encode($this->getAttributes(), $options);
    }

    public function formName()
    {
        $reflector = new \ReflectionClass($this);
        if (PHP_VERSION_ID >= 70000 && $reflector->isAnonymous()) {
            throw new InvalidConfigException('The "formName()" method should be explicitly defined for anonymous models');
        }
        return $reflector->getShortName();
    }

    public function rules()
    {
        return [];
    }

    public function validate()
    {
        $rulesArr = $this->rules();
        foreach ($rulesArr as $key => $eachRule) {
            if (is_array($eachRule[0])) {//required integer string
                $attributesArr = $eachRule[0];
                $dataType = $eachRule[1];
                if ($dataType == self::TYPE_INTEGER) {//integer
                    foreach ($attributesArr as $attributeName) {
                        if (!is_numeric($this->$attributeName)) {
                            return false;
                        }
                    }
                } elseif ($dataType == self::TYPE_REQUIRED) {//required
                    foreach ($attributesArr as $attributeName) {
                        if (!isset($this->$attributeName)) {
                            return false;
                        }
                    }
                } elseif ($dataType == self::TYPE_STRING) {//string 不作判断

                } else {//字段类型错误
                    return false;
                }
            } elseif (isset($eachRule[1]) && $eachRule[1] == self::TYPE_IN) {
                $attributeName = $eachRule[0];
                if (!in_array($this->$attributeName, $eachRule['range'])) {
                    return false;
                }

            } else{//数组格式错误
                return false;
            }

        }
        return true;
    }

}