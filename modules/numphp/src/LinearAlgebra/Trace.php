<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Trace
{
    /**
     * Return the sum along diagonals of the array.
     * Currently supports 2-D arrays.
     *
     * @param NDArray $a
     * @return float
     */
    public static function trace(NDArray $a): float
    {
        $shape = $a->getShape();
        if (count($shape) !== 2) {
            throw new \Exception("Trace currently implemented for 2D arrays only");
        }
        $data = $a->getData();
        $sum = 0;
        $limit = min($shape[0], $shape[1]);
        for ($i = 0; $i < $limit; $i++) {
            $sum += $data[$i][$i];
        }
        return (float)$sum;
    }
}