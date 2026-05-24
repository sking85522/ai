<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class ColumnStack
{
    /**
     * Stack 1-D arrays as columns into a 2-D array.
     *
     * @param array $tup
     * @return NDArray
     */
    public static function column_stack(array $tup): NDArray
    {
        $reshaped = array_map(function($arr) {
            return Reshape::reshape($arr, [array_product($arr->getShape()), 1]);
        }, $tup);
        return Hstack::hstack($reshaped);
    }
}