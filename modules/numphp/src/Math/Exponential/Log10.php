<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;

class Log10
{
    /**
     * Return the base 10 logarithm of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log10(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveLog10($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveLog10($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveLog10'], $data);
        }
        return log10($data);
    }
}