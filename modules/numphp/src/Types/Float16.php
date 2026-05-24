<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Float16
{
    public static function float16(...$args)
    {
        return \NumPHP\NumPHP::float16(...$args);
    }
}
