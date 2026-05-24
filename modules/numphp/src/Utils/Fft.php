<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Fft
{
    public static function fft(...$args)
    {
        return \NumPHP\NumPHP::fft(...$args);
    }
}
