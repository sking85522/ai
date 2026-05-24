<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Max
{
    /**
     * Return the maximum of an array or maximum along an axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function max(NDArray $a, ?int $axis = null)
    {
        if ($axis === null) {
            $flat = Flatten::flatten($a);
            $data = $flat->getData();
            if (!is_array($data)) return $data;
            return max($data);
        }

        $data = $a->getData();
        $shape = $a->getShape();

        // 1D Array
        if (count($shape) === 1) {
            return max($data);
        }

        // 2D Array
        if (count($shape) === 2) {
            if ($axis === 0) {
                // Max of columns
                $cols = $shape[1];
                $result = array_fill(0, $cols, -INF);
                foreach ($data as $row) {
                    for ($i = 0; $i < $cols; $i++) {
                        if ($row[$i] > $result[$i]) $result[$i] = $row[$i];
                    }
                }
                return new NDArray($result, $a->dtype());
            } elseif ($axis === 1 || $axis === -1) {
                // Max of rows
                $result = array_map('max', $data);
                return new NDArray($result, $a->dtype());
            }
        }

        throw new \Exception("Max currently only supports axis for 1D and 2D arrays.");
    }
}