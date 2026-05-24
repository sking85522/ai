<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Signbit
{
    public static function signbit(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) {
            return is_nan($x) ? false : ($x < 0);
        });
        return new NDArray($data, 'bool');
    }
}
