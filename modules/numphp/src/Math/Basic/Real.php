<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Real
{
    public static function real(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return $x; });
        return new NDArray($data);
    }
}
