<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Unique
{
    public static function unique(NDArray $a): NDArray
    {
        $flat = [];
        Helpers::flatten($a->getData(), $flat);
        $uniq = array_values(array_unique($flat));
        return new NDArray($uniq);
    }
}
