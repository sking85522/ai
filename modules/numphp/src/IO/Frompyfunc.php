<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Frompyfunc
{
    public static function frompyfunc(...$args)
    {
        return \NumPHP\NumPHP::frompyfunc(...$args);
    }
}
