<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Absolute
{
    public static function absolute(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return abs($x); });
        return new NDArray($data);
    }
}
