<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class OnesLike
{
    public static function ones_like(...$args)
    {
        return \NumPHP\NumPHP::ones_like(...$args);
    }
}
