<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class EinsumPath
{
    public static function einsum_path(...$args)
    {
        return \NumPHP\NumPHP::einsum_path(...$args);
    }
}
