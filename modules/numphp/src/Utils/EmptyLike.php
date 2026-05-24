<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class EmptyLike
{
    public static function empty_like(...$args)
    {
        return \NumPHP\NumPHP::empty_like(...$args);
    }
}
