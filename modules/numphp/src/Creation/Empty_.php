<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Empty_
{
    public static function empty(array $shape, string $dtype = null): NDArray
    {
        $data = Helpers::buildFilled($shape, null);
        return new NDArray($data, $dtype);
    }
}
