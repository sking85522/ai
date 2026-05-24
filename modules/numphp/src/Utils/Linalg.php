<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Linalg
{
    public static function linalg(...$args)
    {
        return \NumPHP\NumPHP::linalg(...$args);
    }
}
