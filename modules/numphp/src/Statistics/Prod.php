<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Prod
{
    /**
     * Product of array elements.
     * Currently supports product of all elements.
     */
    public static function prod(NDArray $a): float
    {
        $flat = Flatten::flatten($a);
        $data = $flat->getData();

        if (!is_array($data)) {
            return (float)$data;
        }

        return (float)array_product($data);
    }
}