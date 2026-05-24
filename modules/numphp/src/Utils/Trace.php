<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Trace
{
    public static function trace(...$args)
    {
        return \NumPHP\NumPHP::trace(...$args);
    }
}
