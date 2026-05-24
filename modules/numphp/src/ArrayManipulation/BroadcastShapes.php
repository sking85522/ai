<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class BroadcastShapes
{
    public static function broadcast_shapes(...$args)
    {
        return \NumPHP\NumPHP::broadcast_shapes(...$args);
    }
}
