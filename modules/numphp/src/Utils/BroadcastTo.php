<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class BroadcastTo
{
    public static function broadcast_to(...$args)
    {
        return \NumPHP\NumPHP::broadcast_to(...$args);
    }
}
