<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Stack
{
    /**
     * Join a sequence of arrays along a new axis.
     *
     * @param array $arrays
     * @param int $axis
     * @return NDArray
     */
    public static function stack(array $arrays, int $axis = 0): NDArray
    {
        $expanded = array_map(function($arr) use ($axis) { return ExpandDims::expand_dims($arr, $axis); }, $arrays);
        return Concatenate::concatenate($expanded, $axis);
    }
}