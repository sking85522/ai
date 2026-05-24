<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanvar
{
    public static function nanvar(...$args)
    {
        return \NumPHP\NumPHP::nanvar(...$args);
    }
}
