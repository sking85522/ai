<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nonzero
{
    public static function nonzero(...$args)
    {
        return \NumPHP\NumPHP::nonzero(...$args);
    }
}
