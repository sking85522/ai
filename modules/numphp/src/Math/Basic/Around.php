<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Around
{
    public static function around(NDArray $a, int $decimals = 0): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) use ($decimals) {
            return round($x, $decimals);
        });
        return new NDArray($data);
    }
}
