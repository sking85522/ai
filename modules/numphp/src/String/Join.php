<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Join
{
    /**
     * Return a string which is the concatenation of the strings in the sequence seq.
     *
     * @param string $sep
     * @param NDArray $seq
     * @return string
     */
    public static function join(string $sep, NDArray $seq): string
    {
        return implode($sep, \NumPHP\ArrayManipulation\Flatten::flatten($seq)->getData());
    }
}