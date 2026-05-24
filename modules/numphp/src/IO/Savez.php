<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Savez
{
    public static function savez(...$args)
    {
        return \NumPHP\NumPHP::savez(...$args);
    }
}
