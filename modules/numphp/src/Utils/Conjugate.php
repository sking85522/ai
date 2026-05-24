<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Conjugate
{
    public static function conjugate(...$args)
    {
        return \NumPHP\NumPHP::conjugate(...$args);
    }
}
