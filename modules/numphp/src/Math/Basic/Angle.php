<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Angle
{
    public static function angle(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) {
            return ($x >= 0) ? 0.0 : M_PI;
        });
        return new NDArray($data);
    }
}
