<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Spacing
{
    public static function spacing(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) {
            if ($x == 0.0) {
                return PHP_FLOAT_MIN;
            }
            return abs($x) * PHP_FLOAT_EPSILON;
        });
        return new NDArray($data);
    }
}
