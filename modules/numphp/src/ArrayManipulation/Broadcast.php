<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Broadcast
{
    public static function broadcast(...$args)
    {
        return \NumPHP\NumPHP::broadcast(...$args);
    }
}
