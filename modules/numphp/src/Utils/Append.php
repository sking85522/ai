<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Append
{
    public static function append(...$args)
    {
        return \NumPHP\NumPHP::append(...$args);
    }
}
