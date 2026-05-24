<?php

namespace SciPHP\Interpolation;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class Linear
{
    /**
     * 1-D linear interpolation.
     * This approximates scipy.interpolate.interp1d.
     *
     * @param NDArray|array $x A 1-D array of real values.
     * @param NDArray|array $y A 1-D array of real values. The length of y must be equal to the length of x.
     * @return callable A function which takes an x-value and returns the interpolated y-value.
     */
    public static function interp1d($x, $y): callable
    {
        // Extract raw data if NDArray is passed
        $x_data = ($x instanceof NDArray) ? $x->getData() : $x;
        $y_data = ($y instanceof NDArray) ? $y->getData() : $y;

        if (count($x_data) !== count($y_data)) {
            throw new \InvalidArgumentException("x and y arrays must have the same length.");
        }

        // Sort the data based on x to ensure strictly increasing x values
        array_multisort($x_data, SORT_ASC, $y_data);

        return function($x_new) use ($x_data, $y_data) {
            // Handle array of new x values
            if (is_array($x_new) || $x_new instanceof NDArray) {
                $x_new_data = ($x_new instanceof NDArray) ? $x_new->getData() : $x_new;
                $result = [];
                foreach ($x_new_data as $val) {
                    $result[] = self::interpolate_single($val, $x_data, $y_data);
                }
                return np::array($result);
            }

            // Handle scalar x value
            return self::interpolate_single($x_new, $x_data, $y_data);
        };
    }

    private static function interpolate_single($x_val, array $x_data, array $y_data)
    {
        $n = count($x_data);

        // Extrapolation / Bounds handling
        if ($x_val <= $x_data[0]) {
            return $y_data[0];
        }
        if ($x_val >= $x_data[$n - 1]) {
            return $y_data[$n - 1];
        }

        // Binary search to find the correct interval
        $low = 0;
        $high = $n - 1;
        while ($low <= $high) {
            $mid = (int)(($low + $high) / 2);
            if ($x_data[$mid] < $x_val) {
                $low = $mid + 1;
            } else if ($x_data[$mid] > $x_val) {
                $high = $mid - 1;
            } else {
                return $y_data[$mid];
            }
        }

        $i = $high; // The interval is [x_data[$i], x_data[$i+1]]

        $x0 = $x_data[$i];
        $x1 = $x_data[$i + 1];
        $y0 = $y_data[$i];
        $y1 = $y_data[$i + 1];

        return $y0 + ($x_val - $x0) * ($y1 - $y0) / ($x1 - $x0);
    }
}
