<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Concat
{
    public static function concat(...$args)
    {
        return \NumPHP\NumPHP::concat(...$args);
    }
}
