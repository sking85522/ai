<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Diff
{
    public static function diff(...$args)
    {
        return \NumPHP\NumPHP::diff(...$args);
    }
}
