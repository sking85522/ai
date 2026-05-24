<?php

namespace SciPHP\Stats;

use SciPHP\Statistics\NormalDist;

/**
 * Normal continuous random variable.
 * Wraps SciPHP\Statistics\NormalDist for scipy-like usage.
 */
class Norm
{
    /**
     * Probability density function.
     */
    public function pdf($x, float $loc = 0.0, float $scale = 1.0)
    {
        return NormalDist::pdf($x, $loc, $scale);
    }

    /**
     * Cumulative distribution function.
     */
    public function cdf($x, float $loc = 0.0, float $scale = 1.0)
    {
        return NormalDist::cdf($x, $loc, $scale);
    }
}
