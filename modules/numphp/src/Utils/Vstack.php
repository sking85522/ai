<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Vstack
{
    public static function vstack(...$args)
    {
        return \NumPHP\NumPHP::vstack(...$args);
    }
}
