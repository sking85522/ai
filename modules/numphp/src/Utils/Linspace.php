<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Linspace
{
    public static function linspace(...$args)
    {
        return \NumPHP\NumPHP::linspace(...$args);
    }
}
