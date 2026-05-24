<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class SavezCompressed
{
    public static function savez_compressed(...$args)
    {
        return \NumPHP\NumPHP::savez_compressed(...$args);
    }
}
