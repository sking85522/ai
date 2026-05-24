<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Dtype
{
    public static function dtype(...$args)
    {
        return \NumPHP\NumPHP::dtype(...$args);
    }
}
