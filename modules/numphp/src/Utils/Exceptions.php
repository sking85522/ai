<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Exceptions
{
    public static function exceptions(...$args)
    {
        return \NumPHP\NumPHP::exceptions(...$args);
    }
}
