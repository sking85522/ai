<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Allclose
{
    public static function allclose(...$args)
    {
        return \NumPHP\NumPHP::allclose(...$args);
    }
}
