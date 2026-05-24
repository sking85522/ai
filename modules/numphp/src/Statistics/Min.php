<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Min
{
    /**
     * Return the minimum of an array or minimum along an axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function min(NDArray $a, ?int $axis = null)
    {
        if ($axis === null) {
            $flat = Flatten::flatten($a);
            $data = $flat->getData();
            if (!is_array($data)) return $data;
            return min($data);
        }

        $data = $a->getData();
        $shape = $a->getShape();

        // 1D Array
        if (count($shape) === 1) {
            return min($data);
        }

        // 2D Array
        if (count($shape) === 2) {
            if ($axis === 0) {
                // Min of columns
                $cols = $shape[1];
                $result = array_fill(0, $cols, INF);
                foreach ($data as $row) {
                    for ($i = 0; $i < $cols; $i++) {
                        if ($row[$i] < $result[$i]) $result[$i] = $row[$i];
                    }
                }
                return new NDArray($result, $a->dtype());
            } elseif ($axis === 1 || $axis === -1) {
                // Min of rows
                $result = array_map('min', $data);
                return new NDArray($result, $a->dtype());
            }
        }

        throw new \Exception("Min currently only supports axis for 1D and 2D arrays.");
    }
}