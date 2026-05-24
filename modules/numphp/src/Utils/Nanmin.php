<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanmin
{
    public static function nanmin(...$args)
    {
        return \NumPHP\NumPHP::nanmin(...$args);
    }
}
