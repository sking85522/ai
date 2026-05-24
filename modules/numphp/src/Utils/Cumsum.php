<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Cumsum
{
    public static function cumsum(...$args)
    {
        return \NumPHP\NumPHP::cumsum(...$args);
    }
}
