<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Clip
{
    public static function clip(...$args)
    {
        return \NumPHP\NumPHP::clip(...$args);
    }
}
