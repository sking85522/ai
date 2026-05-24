<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Transpose
{
    public static function transpose(NDArray $a): NDArray
    {
        $shape = $a->getShape();

        if (count($shape) !== 2) {
             // For 1D, transpose is the same. For >2D, we need permutation logic.
             // Returning as is for 1D or implementing basic 2D swap
             if (count($shape) === 1) {
                 return $a;
             }
             throw new \Exception("Transpose currently only implemented for 1D and 2D arrays.");
        }

        $data = $a->getData();
        $rows = $shape[0];
        $cols = $shape[1];

        $result = [];

        for ($j = 0; $j < $cols; $j++) {
            $result[$j] = [];
            for ($i = 0; $i < $rows; $i++) {
                $result[$j][$i] = $data[$i][$j];
            }
        }

        return new NDArray($result, $a->getDType());
    }
}
