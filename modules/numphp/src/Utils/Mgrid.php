<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Mgrid
{
    public static function mgrid(...$args)
    {
        return \NumPHP\NumPHP::mgrid(...$args);
    }
}
