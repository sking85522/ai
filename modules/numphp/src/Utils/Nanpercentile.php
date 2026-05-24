<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanpercentile
{
    public static function nanpercentile(...$args)
    {
        return \NumPHP\NumPHP::nanpercentile(...$args);
    }
}
