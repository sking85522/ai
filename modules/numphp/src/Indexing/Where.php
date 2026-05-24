<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Where
{
    /**
     * Return elements chosen from x or y depending on condition.
     *
     * @param NDArray $condition (Must be broadcastable or same size, strictly same size/flat for this v1)
     * @param NDArray|float|int $x
     * @param NDArray|float|int $y
     * @return NDArray
     */
    public static function where(NDArray $condition, $x, $y): NDArray
    {
        // For simplicity v1: Flatten everything and operate 1D
        // This avoids complex broadcasting logic implementation in v1
        $flatCond = Flatten::flatten($condition)->getData();

        // Handle X
        if ($x instanceof NDArray) {
            $flatX = Flatten::flatten($x)->getData();
        } else {
            $flatX = array_fill(0, count($flatCond), $x);
        }

        // Handle Y
        if ($y instanceof NDArray) {
            $flatY = Flatten::flatten($y)->getData();
        } else {
            $flatY = array_fill(0, count($flatCond), $y);
        }

        if (count($flatX) !== count($flatCond) || count($flatY) !== count($flatCond)) {
             throw new \InvalidArgumentException("Shapes must match for where() in this version");
        }

        $result = [];
        $n = count($flatCond);

        for ($i = 0; $i < $n; $i++) {
            // Check condition (truthy check)
            if ($flatCond[$i]) {
                $result[] = $flatX[$i];
            } else {
                $result[] = $flatY[$i];
            }
        }

        // Note: Returning 1D array. A reshape would be needed to match original shape.
        // Ideally we should preserve shape of condition.
        return new NDArray($result);
    }
}