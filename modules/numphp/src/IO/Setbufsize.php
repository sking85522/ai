<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Setbufsize
{
    public static function setbufsize(...$args)
    {
        return \NumPHP\NumPHP::setbufsize(...$args);
    }
}
