<?php

namespace App\Utils;


use Swoft\App;
use Swoft\Exception\Exception;

class Config
{
    protected static $versions = null;

    protected static $json = [];

//    public static function loadJson($name)
//    {
//        $configVersion = commonVar()->getConfigVersion() ?? '1.0.0';
//        if (!isset (self::$json [$name])) {
//            $dataDir = App::getAlias("@root/res/data");
//            $versions = is_null(self::$versions) ? self::getVersions() : self::$versions;
//            foreach ($versions as $version) {
//                $file = $dataDir . DS . $version . DS . $name . '.json';
//                if (file_exists($file)) {
//                    self::$json [$name][$version] = json_decode(file_get_contents($file), true);
//                }
//            }
//            if (isset(self::$json [$name])) {
//                krsort(self::$json [$name]);
//            }
//        }
//
//
//        if (isset (self::$json [$name][$configVersion])) {
//            return self::$json [$name][$configVersion];
//        } elseif (isset(self::$json [$name])) {
//            foreach (self::$json [$name] as $version => $value) {
//                if ($version < $configVersion) {
//                    return $value;
//                }
//            }
//        }
//
//        throw new Exception("config file $name.json does not exists,config version is $configVersion!");
//    }
    public static function loadJson($name)
    {

        if (!isset (self::$json [$name])) {
            $dataDir = App::getAlias("@root/res/data");
            $file = $dataDir . DS . $name . '.json';
            if (file_exists($file)) {
                self::$json [$name] = json_decode(file_get_contents($file), true);
                return  self::$json [$name];
            }
            throw new Exception("config file $name.json does not exists!");
        }else{
            return  self::$json [$name];
        }

    }

    public static function getVersions()
    {
        $dir = App::getAlias("@root/res/data");
        self::$versions = array_filter(scandir($dir), function ($v) {
            return !in_array($v, ['.', '..']);
        });
        return self::$versions;
    }
}