<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Imag
{
    public static function imag(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return 0; });
        return new NDArray($data);
    }
}
