<?php

namespace NumPHP\Math\Calculus;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Gradient
{
    /**
     * Return the gradient of an N-dimensional array.
     * Currently supports 1D arrays only.
     *
     * @param NDArray $f
     * @return NDArray
     */
    public static function gradient(NDArray $f): NDArray
    {
        $data = Flatten::flatten($f)->getData();
        $n = count($data);

        if ($n === 0) return new NDArray([]);
        if ($n === 1) return new NDArray([0.0]);

        $grad = [];

        // First point (forward difference)
        $grad[0] = $data[1] - $data[0];

        // Central points (central difference)
        for ($i = 1; $i < $n - 1; $i++) {
            $grad[$i] = ($data[$i + 1] - $data[$i - 1]) / 2.0;
        }

        // Last point (backward difference)
        $grad[$n - 1] = $data[$n - 1] - $data[$n - 2];

        return new NDArray($grad, 'float');
    }
}