<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;

class Expm1
{
    /**
     * Calculate exp(x) - 1 for all elements in the array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function expm1(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveExpm1($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveExpm1($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveExpm1'], $data);
        }
        return expm1($data);
    }
}