<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Inexact
{
    public static function inexact(...$args)
    {
        return \NumPHP\NumPHP::inexact(...$args);
    }
}
