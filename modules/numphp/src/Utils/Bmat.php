<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Bmat
{
    public static function bmat(...$args)
    {
        return \NumPHP\NumPHP::bmat(...$args);
    }
}
