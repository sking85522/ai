<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nansum
{
    public static function nansum(...$args)
    {
        return \NumPHP\NumPHP::nansum(...$args);
    }
}
