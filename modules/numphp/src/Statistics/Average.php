<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Average
{
    /**
     * Compute the weighted average along the specified axis.
     * Currently supports flattened array (axis=null).
     *
     * @param NDArray $a
     * @param NDArray|array|null $weights
     * @return float
     */
    public static function average(NDArray $a, $weights = null): float
    {
        if ($weights === null) {
            return Mean::mean($a);
        }

        $flatA = Flatten::flatten($a)->getData();

        if ($weights instanceof NDArray) {
            $flatW = Flatten::flatten($weights)->getData();
        } else {
            $flatW = $weights;
        }

        if (!is_array($flatA)) $flatA = [$flatA];
        if (!is_array($flatW)) $flatW = [$flatW];

        if (count($flatA) !== count($flatW)) {
            throw new \InvalidArgumentException("Weights array shape must match data array shape");
        }

        $sum = 0;
        $wSum = 0;
        $n = count($flatA);

        for ($i = 0; $i < $n; $i++) {
            $sum += $flatA[$i] * $flatW[$i];
            $wSum += $flatW[$i];
        }

        if ($wSum == 0) throw new \Exception("Weights sum to zero, can't normalize");

        return $sum / $wSum;
    }
}