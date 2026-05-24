<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;

class Log
{
    public static function log(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveLog($data);
        return new NDArray($resultData);
    }

    private static function recursiveLog($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveLog($value);
            }
            return $result;
        }
        return log($data);
    }
}