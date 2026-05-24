<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Dstack
{
    public static function dstack(array $tup): NDArray
    {
        $reshaped = array_map(function($arr) {
            return Atleast3d::atleast_3d($arr);
        }, $tup);

        return Concatenate::concatenate($reshaped, 2);
    }
}