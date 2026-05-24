<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Inf
{
    public static function inf(...$args)
    {
        return \NumPHP\NumPHP::inf(...$args);
    }
}
