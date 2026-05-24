<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Quantile
{
    /**
     * Compute the q-th quantile of the data.
     *
     * @param NDArray $a
     * @param float $q Quantile to compute, which must be between 0 and 1 inclusive.
     * @return float
     */
    public static function quantile(NDArray $a, float $q): float
    {
        if ($q < 0.0 || $q > 1.0) {
            throw new \InvalidArgumentException("Quantile must be between 0.0 and 1.0.");
        }
        return Percentile::percentile($a, $q * 100);
    }
}