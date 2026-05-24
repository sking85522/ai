<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Deg2Rad
{
    public static function deg2rad(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return $x * M_PI / 180.0; });
        return new NDArray($data);
    }
}
