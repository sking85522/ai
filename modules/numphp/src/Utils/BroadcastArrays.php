<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class BroadcastArrays
{
    public static function broadcast_arrays(...$args)
    {
        return \NumPHP\NumPHP::broadcast_arrays(...$args);
    }
}
