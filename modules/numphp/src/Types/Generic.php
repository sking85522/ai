<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Generic
{
    public static function generic(...$args)
    {
        return \NumPHP\NumPHP::generic(...$args);
    }
}
