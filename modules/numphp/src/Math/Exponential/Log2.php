<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;

class Log2
{
    /**
     * Return the base 2 logarithm of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log2(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveLog2($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveLog2($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveLog2'], $data);
        }
        return log($data, 2);
    }
}