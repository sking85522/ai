<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Dot
{
    public static function dot(...$args)
    {
        return \NumPHP\NumPHP::dot(...$args);
    }
}
