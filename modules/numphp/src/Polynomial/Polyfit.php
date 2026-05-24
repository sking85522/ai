<?php

namespace NumPHP\Polynomial;

use NumPHP\Core\NDArray;
use NumPHP\LinearAlgebra\Lstsq;

class Polyfit
{
    /**
     * Least squares polynomial fit.
     *
     * @param NDArray $x
     * @param NDArray $y
     * @param int $deg Degree of the fitting polynomial
     * @return NDArray Polynomial coefficients, highest power first.
     */
    public static function polyfit(NDArray $x, NDArray $y, int $deg): NDArray
    {
        $x_data = \NumPHP\ArrayManipulation\Flatten::flatten($x)->getData();
        $y_data = \NumPHP\ArrayManipulation\Flatten::flatten($y)->getData();

        if (count($x_data) !== count($y_data)) {
            throw new \InvalidArgumentException("Inputs x and y must have same length.");
        }
        if (count($x_data) < $deg + 1) {
            throw new \InvalidArgumentException("More degrees than data points.");
        }

        // Create Vandermonde matrix
        $v_data = [];
        foreach ($x_data as $val) {
            $row = [];
            for ($i = $deg; $i >= 0; $i--) {
                $row[] = pow($val, $i);
            }
            $v_data[] = $row;
        }
        $v = new NDArray($v_data);

        // Solve the system V * c = y for coefficients c
        $coeffs = Lstsq::lstsq($v, new NDArray($y_data));

        return \NumPHP\ArrayManipulation\Flatten::flatten($coeffs);
    }
}
