<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Seterrcall
{
    public static function seterrcall(...$args)
    {
        return \NumPHP\NumPHP::seterrcall(...$args);
    }
}
