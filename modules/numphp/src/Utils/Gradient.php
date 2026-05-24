<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Gradient
{
    public static function gradient(...$args)
    {
        return \NumPHP\NumPHP::gradient(...$args);
    }
}
