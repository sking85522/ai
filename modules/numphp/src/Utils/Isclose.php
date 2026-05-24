<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Isclose
{
    public static function isclose(...$args)
    {
        return \NumPHP\NumPHP::isclose(...$args);
    }
}
