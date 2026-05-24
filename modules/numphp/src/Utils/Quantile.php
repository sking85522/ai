<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Quantile
{
    public static function quantile(...$args)
    {
        return \NumPHP\NumPHP::quantile(...$args);
    }
}
