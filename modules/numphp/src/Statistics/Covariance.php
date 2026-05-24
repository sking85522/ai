<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Covariance
{
    /**
     * Estimate a covariance matrix, given data and weights.
     *
     * @param NDArray $m
     * @return NDArray
     */
    public static function cov(NDArray $m): NDArray
    {
        $shape = $m->getShape();

        // If 1D, treat as a single variable observation
        if (count($shape) === 1) {
            $v = Var_::var($m); // Variance of single variable
            // cov return shape usually 2D or scalar? NumPy returns 0-D array (scalar) for 1D input
            return new NDArray($v);
        }

        // Assume rows are variables, columns are observations (NumPy default)
        $rows = $shape[0];
        $cols = $shape[1]; // N observations
        $data = $m->getData();

        // Subtract mean from each row
        $means = array_map(function($row) use ($cols) { return array_sum($row) / $cols; }, $data);

        $cov = [];
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $rows; $j++) {
                $sum = 0;
                for ($k = 0; $k < $cols; $k++) {
                    $sum += ($data[$i][$k] - $means[$i]) * ($data[$j][$k] - $means[$j]);
                }
                $cov[$i][$j] = $sum / ($cols - 1);
            }
        }
        return new NDArray($cov);
    }
}