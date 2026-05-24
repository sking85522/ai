<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanstd
{
    public static function nanstd(...$args)
    {
        return \NumPHP\NumPHP::nanstd(...$args);
    }
}
