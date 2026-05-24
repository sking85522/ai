<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Geterr
{
    public static function geterr(...$args)
    {
        return \NumPHP\NumPHP::geterr(...$args);
    }
}
