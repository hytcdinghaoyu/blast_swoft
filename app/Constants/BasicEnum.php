<?php

namespace App\Constants;

use yii\helpers\Inflector;

abstract class BasicEnum
{
    private static $constCacheArray = null;

    private function __construct()
    {
        /*
          Preventing instance :)
        */
    }

    /**
     * 获取定义的const列表
     * example: {'ITEM_ID' => 1283}
     * @return mixed
     */
    private static function getConstants()
    {
        if (self::$constCacheArray == null) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    public static function getConstantsValues()
    {
        return array_values(self::getConstants());
    }

    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));

        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value)
    {
        $values = array_values(self::getConstants());

        return in_array($value, $values, $strict = true);
    }

    /**
     * 根据value取对应的string值，先全部转成小写，再把首字母大写
     * @param $value
     * @return string
     */
    public static function valueToCamelStr($value)
    {
        $constants = self::getConstants();

        if (!in_array($value, $constants)) {
            return '';
        }

        return Inflector::camelize(strtolower(array_search($value, $constants)));

    }

    /**
     * 根据value取对应的string值，小写形式
     * @param $value
     * @return string
     */
    public static function valueToLowerStr($value)
    {
        $constants = self::getConstants();

        if (!in_array($value, $constants)) {
            return '';
        }

        return strtolower(array_search($value, $constants));

    }
    /**
     * 根据value取对应的string值，不改变大小写
     * @param $value
     * @return string
     */
    public static function valueToStr($value)
    {
        $constants = self::getConstants();

        if (!in_array($value, $constants)) {
            return '';
        }

        return array_search($value, $constants);

    }

    /**
     * 不区分大小写，取key对应的int值
     * @param $name
     * @return int
     */
    public static function getValueByName($name)
    {

        $constants = self::getConstants();

        $name = strtoupper($name);

        return isset($constants[$name]) ? $constants[$name] : 0;
    }
}