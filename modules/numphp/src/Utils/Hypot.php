<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Hypot
{
    public static function hypot(...$args)
    {
        return \NumPHP\NumPHP::hypot(...$args);
    }
}
