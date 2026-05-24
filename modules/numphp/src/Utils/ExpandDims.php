<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class ExpandDims
{
    public static function expand_dims(...$args)
    {
        return \NumPHP\NumPHP::expand_dims(...$args);
    }
}
