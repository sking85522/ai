<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Negative
{
    /**
     * Numerical negative, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function negative(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveNegative($data);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveNegative($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveNegative'], $data);
        }
        return -$data;
    }
}