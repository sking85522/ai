<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Corrcoef
{
    public static function corrcoef(...$args)
    {
        return \NumPHP\NumPHP::corrcoef(...$args);
    }
}
