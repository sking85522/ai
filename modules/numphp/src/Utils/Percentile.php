<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Percentile
{
    public static function percentile(...$args)
    {
        return \NumPHP\NumPHP::percentile(...$args);
    }
}
