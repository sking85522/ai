<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Errstate
{
    public static function errstate(...$args)
    {
        return \NumPHP\NumPHP::errstate(...$args);
    }
}
