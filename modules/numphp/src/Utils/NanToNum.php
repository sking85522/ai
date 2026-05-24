<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class NanToNum
{
    public static function nan_to_num(...$args)
    {
        return \NumPHP\NumPHP::nan_to_num(...$args);
    }
}
