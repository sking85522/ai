<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;

class Log1p
{
    /**
     * Return the natural logarithm of one plus the input array, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log1p(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveLog1p($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveLog1p($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveLog1p'], $data);
        }
        return log1p($data);
    }
}