<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class FromDlpack
{
    public static function from_dlpack(...$args)
    {
        return \NumPHP\NumPHP::from_dlpack(...$args);
    }
}
