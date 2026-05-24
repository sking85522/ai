<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Rint
{
    public static function rint(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) {
            return round($x, 0, PHP_ROUND_HALF_EVEN);
        });
        return new NDArray($data);
    }
}
