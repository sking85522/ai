<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Sinc
{
    /**
     * Return the sinc function. sinc(x) = sin(pi*x)/(pi*x).
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function sinc(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveOp($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveOp($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveOp'], $data);
        }
        $val = $data * M_PI;
        return ($val == 0) ? 1.0 : sin($val) / $val;
    }
}