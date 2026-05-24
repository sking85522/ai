<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Isposinf
{
    public static function isposinf(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return is_infinite($x) && $x > 0; });
        return new NDArray($data, 'bool');
    }
}
