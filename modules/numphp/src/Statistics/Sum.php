<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Sum
{
    /**
     * Sum of array elements.
     *
     * @param NDArray $a
     * @param int|null $axis Axis along which the sum is performed.
     * @return float|NDArray
     */
    public static function sum(NDArray $a, ?int $axis = null)
    {
        if ($axis === null) {
            $flat = Flatten::flatten($a);
            $data = $flat->getData();

            if (!is_array($data)) {
                return (float)$data;
            }
            return (float)array_sum($data);
        }

        $data = $a->getData();
        $shape = $a->getShape();

        // 1D Array
        if (count($shape) === 1) {
            return (float)array_sum($data);
        }

        // 2D Array
        if (count($shape) === 2) {
            // Axis 0: Sum columns -> returns [sum(col0), sum(col1), ...]
            if ($axis === 0) {
                $cols = $shape[1];
                $result = array_fill(0, $cols, 0);
                foreach ($data as $row) {
                    for ($i = 0; $i < $cols; $i++) {
                        $result[$i] += $row[$i];
                    }
                }
                return new NDArray($result, $a->dtype());
            }

            // Axis 1: Sum rows -> returns [sum(row0), sum(row1), ...]
            if ($axis === 1 || $axis === -1) {
                $result = array_map(function($row) {
                    return array_sum($row);
                }, $data);
                return new NDArray($result, $a->dtype());
            }
        }

        // Fallback for higher dimensions or unsupported axes (for now)
        // Flattening is the safe default if axis logic isn't fully generic yet
        $flat = Flatten::flatten($a);
        $data = $flat->getData();
        return (float)array_sum($data);
    }
}