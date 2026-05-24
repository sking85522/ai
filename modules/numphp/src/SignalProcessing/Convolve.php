<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Convolve
{
    /**
     * Returns the discrete, linear convolution of two one-dimensional sequences.
     *
     * @param NDArray $a
     * @param NDArray $v
     * @return NDArray
     */
    public static function convolve(NDArray $a, NDArray $v): NDArray
    {
        $a_data = Flatten::flatten($a)->getData();
        $v_data = Flatten::flatten($v)->getData();
        $na = count($a_data);
        $nv = count($v_data);
        $result = array_fill(0, $na + $nv - 1, 0);

        for ($i = 0; $i < $na; $i++) {
            for ($j = 0; $j < $nv; $j++) {
                $result[$i + $j] += $a_data[$i] * $v_data[$j];
            }
        }
        return new NDArray($result);
    }
}