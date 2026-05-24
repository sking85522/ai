<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Exp2
{
    public static function exp2(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return pow(2, $x); });
        return new NDArray($data);
    }
}
