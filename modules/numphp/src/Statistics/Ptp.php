<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Ptp
{
    /**
     * Range of values (maximum - minimum) along an axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function ptp(NDArray $a, ?int $axis = null)
    {
        $max = Max::max($a, $axis);
        $min = Min::min($a, $axis);
        if ($max instanceof NDArray) {
            return \NumPHP\Math\Basic\Subtract::subtract($max, $min);
        }
        return $max - $min;
    }
}