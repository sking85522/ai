<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Flatnonzero
{
    public static function flatnonzero(...$args)
    {
        return \NumPHP\NumPHP::flatnonzero(...$args);
    }
}
