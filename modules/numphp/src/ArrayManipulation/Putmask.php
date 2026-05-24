<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Putmask
{
    public static function putmask(NDArray &$a, NDArray $mask, $values)
    {
        return \NumPHP\NumPHP::putmask($a, $mask, $values);
    }
}
