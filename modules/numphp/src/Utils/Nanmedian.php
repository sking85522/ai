<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanmedian
{
    public static function nanmedian(...$args)
    {
        return \NumPHP\NumPHP::nanmedian(...$args);
    }
}
