<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Fix
{
    public static function fix(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) {
            return ($x >= 0) ? floor($x) : ceil($x);
        });
        return new NDArray($data);
    }
}
