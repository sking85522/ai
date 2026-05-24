<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanmean
{
    public static function nanmean(...$args)
    {
        return \NumPHP\NumPHP::nanmean(...$args);
    }
}
