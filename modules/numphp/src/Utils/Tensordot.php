<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Tensordot
{
    public static function tensordot(...$args)
    {
        return \NumPHP\NumPHP::tensordot(...$args);
    }
}
