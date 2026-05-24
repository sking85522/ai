<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Concatenate
{
    public static function concatenate(...$args)
    {
        return \NumPHP\NumPHP::concatenate(...$args);
    }
}
