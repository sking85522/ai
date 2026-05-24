<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Nancumsum
{
    /**
     * Return the cumulative sum of the elements, treating NaNs as zero.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function nancumsum(NDArray $a): NDArray
    {
        $data = \NumPHP\ArrayManipulation\Flatten::flatten($a)->getData();
        $sum = 0;
        $result = [];
        foreach ($data as $val) {
            if (!is_nan($val)) {
                $sum += $val;
            }
            $result[] = $sum;
        }
        return new NDArray($result, $a->getDtype());
    }
}