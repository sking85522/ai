<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Identity
{
    public static function identity(...$args)
    {
        return \NumPHP\NumPHP::identity(...$args);
    }
}
