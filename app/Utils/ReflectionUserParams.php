<?php
/**
 * Created by PhpStorm.
 * User: weiqiang
 * Date: 2019/1/2
 * Time: 下午7:33
 */

namespace App\Utils;

class ReflectionUserParams
{
    private $funcsParams = [];

    /**
     * @param $handler
     * @param $params
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getFuncParams(string $handler, array $params): array
    {
        if (!isset($this->funcsParams[$handler])) {
            [$controller, $method] = explode('@', $handler);
            $reflectParams = (new \ReflectionMethod($controller, $method))->getParameters();

            $paramKeys = [];
            foreach ($reflectParams as $key => $reflectParam) {
                $reflectType = $reflectParam->getType();
                $name = $reflectParam->getName();


                if ($reflectType === null) {
                    $paramKeys[] = $name;
                } elseif (strpos($reflectType->__toString(), '\\') === false) {
                    $paramKeys[] = $name;
                }
            }
            $this->funcsParams[$handler] = $paramKeys;
        } else {
            $paramKeys = $this->funcsParams[$handler];
        }

        $matches = [];
        for ($n = 0; $n < min(count($params), count($paramKeys)); $n++) {
            $matches[$paramKeys[$n]] = $params[$n];
        }

        return $matches;
    }
}