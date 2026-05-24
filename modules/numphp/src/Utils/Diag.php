<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Diag
{
    public static function diag(...$args)
    {
        return \NumPHP\NumPHP::diag(...$args);
    }
}
