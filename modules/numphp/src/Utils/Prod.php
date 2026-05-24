<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Prod
{
    public static function prod(...$args)
    {
        return \NumPHP\NumPHP::prod(...$args);
    }
}
