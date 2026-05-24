<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class NotEqual
{
    public static function not_equal(...$args)
    {
        return \NumPHP\NumPHP::not_equal(...$args);
    }
}
