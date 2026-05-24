<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Typename
{
    public static function typename(...$args)
    {
        return \NumPHP\NumPHP::typename(...$args);
    }
}
