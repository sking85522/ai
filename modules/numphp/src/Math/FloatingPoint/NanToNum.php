<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;

class NanToNum
{
    /**
     * Replace NaN with zero and infinity with large finite numbers.
     *
     * @param NDArray $a
     * @param float $nan
     * @param float|null $posinf
     * @param float|null $neginf
     * @return NDArray
     */
    public static function nan_to_num(NDArray $a, float $nan = 0.0, ?float $posinf = null, ?float $neginf = null): NDArray
    {
        // Default large finite number for infinity if not provided
        $posinf = $posinf ?? 1.7976931348623157E+308; // PHP_FLOAT_MAX
        $neginf = $neginf ?? -1.7976931348623157E+308; // -PHP_FLOAT_MAX

        $data = $a->getData();

        $rec = function($item) use (&$rec, $nan, $posinf, $neginf) {
            if (is_array($item)) {
                return array_map($rec, $item);
            }

            if (is_nan($item)) {
                return $nan;
            }
            if (is_infinite($item)) {
                return $item > 0 ? $posinf : $neginf;
            }
            return $item;
        };

        return new NDArray($rec($data), 'float');
    }
}