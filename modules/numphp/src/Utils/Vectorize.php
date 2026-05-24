<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Vectorize
{
    public static function vectorize(...$args)
    {
        return \NumPHP\NumPHP::vectorize(...$args);
    }
}
