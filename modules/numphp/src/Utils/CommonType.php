<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class CommonType
{
    public static function common_type(...$args)
    {
        return \NumPHP\NumPHP::common_type(...$args);
    }
}
