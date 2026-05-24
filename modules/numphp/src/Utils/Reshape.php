<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Reshape
{
    public static function reshape(...$args)
    {
        return \NumPHP\NumPHP::reshape(...$args);
    }
}
