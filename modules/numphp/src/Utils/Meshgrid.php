<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Meshgrid
{
    public static function meshgrid(...$args)
    {
        return \NumPHP\NumPHP::meshgrid(...$args);
    }
}
