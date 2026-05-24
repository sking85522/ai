<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nextafter
{
    public static function nextafter(...$args)
    {
        return \NumPHP\NumPHP::nextafter(...$args);
    }
}
