<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanprod
{
    public static function nanprod(...$args)
    {
        return \NumPHP\NumPHP::nanprod(...$args);
    }
}
