<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Record
{
    public static function record(...$args)
    {
        return \NumPHP\NumPHP::record(...$args);
    }
}
