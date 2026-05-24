<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Rad2Deg
{
    public static function rad2deg(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return $x * 180.0 / M_PI; });
        return new NDArray($data);
    }
}
