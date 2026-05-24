<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Char
{
    public static function char($items): NDArray
    {
        // In NumPy this creates a chararray, here we just wrap as NDArray of strings for now
        return new NDArray($items, 'string');
    }
}