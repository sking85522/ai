<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Histogram
{
    /**
     * Compute the histogram of a set of data.
     *
     * @param NDArray $a
     * @param int $bins
     * @return array [NDArray of values, NDArray of bin edges]
     */
    public static function histogram(NDArray $a, int $bins = 10): array
    {
        $data = Flatten::flatten($a)->getData();
        if (empty($data)) {
            return [new NDArray(array_fill(0, $bins, 0)), new NDArray(range(0, $bins))];
        }

        $min = min($data);
        $max = max($data);

        if ($min === $max) {
            $min -= 0.5;
            $max += 0.5;
        }

        $binWidth = ($max - $min) / $bins;
        $binEdges = array_map(function($i) use ($min, $binWidth) { return $min + $i * $binWidth; }, range(0, $bins));

        $hist = array_fill(0, $bins, 0);
        foreach ($data as $val) {
            $binIndex = ($val == $max) ? ($bins - 1) : floor(($val - $min) / $binWidth);
            if ($binIndex >= 0 && $binIndex < $bins) {
                $hist[(int)$binIndex]++;
            }
        }

        return [new NDArray($hist), new NDArray($binEdges)];
    }
}