<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nan
{
    public static function nan(...$args)
    {
        return \NumPHP\NumPHP::nan(...$args);
    }
}
