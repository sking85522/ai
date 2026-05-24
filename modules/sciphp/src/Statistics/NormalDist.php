<?php

namespace SciPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class NormalDist
{
    /**
     * Probability Density Function for normal distribution.
     * Approximates scipy.stats.norm.pdf
     *
     * @param float|NDArray|array $x Quantiles
     * @param float $loc Mean (default 0)
     * @param float $scale Standard deviation (default 1)
     * @return float|NDArray
     */
    public static function pdf($x, float $loc = 0.0, float $scale = 1.0)
    {
        if (is_array($x) || $x instanceof NDArray) {
            $x_data = ($x instanceof NDArray) ? $x->getData() : $x;
            $result = [];
            foreach ($x_data as $val) {
                $result[] = self::pdf_single($val, $loc, $scale);
            }
            return np::array($result);
        }
        return self::pdf_single($x, $loc, $scale);
    }

    private static function pdf_single(float $x, float $loc, float $scale): float
    {
        $coefficient = 1.0 / ($scale * sqrt(2 * M_PI));
        $exponent = -0.5 * pow(($x - $loc) / $scale, 2);
        return $coefficient * exp($exponent);
    }

    /**
     * Cumulative Distribution Function for normal distribution.
     * Approximates scipy.stats.norm.cdf
     *
     * @param float|NDArray|array $x Quantiles
     * @param float $loc Mean
     * @param float $scale Standard deviation
     * @return float|NDArray
     */
    public static function cdf($x, float $loc = 0.0, float $scale = 1.0)
    {
        if (is_array($x) || $x instanceof NDArray) {
            $x_data = ($x instanceof NDArray) ? $x->getData() : $x;
            $result = [];
            foreach ($x_data as $val) {
                $result[] = self::cdf_single($val, $loc, $scale);
            }
            return np::array($result);
        }
        return self::cdf_single($x, $loc, $scale);
    }

    private static function cdf_single(float $x, float $loc, float $scale): float
    {
        // Using complementary error function (erfc) approximation
        $z = ($x - $loc) / ($scale * sqrt(2));

        // PHP doesn't have a native erfc function in all setups, let's implement a numerical approximation
        $t = 1.0 / (1.0 + 0.5 * abs($z));
        $ans = $t * exp(-$z * $z - 1.26551223 +
                $t * (1.00002368 +
                $t * (0.37409196 +
                $t * (0.09678418 +
                $t * (-0.18628806 +
                $t * (0.27886807 +
                $t * (-1.13520398 +
                $t * (1.48851587 +
                $t * (-0.82215223 +
                $t * 0.17087277)))))))));

        $erfc = ($z >= 0) ? $ans : 2.0 - $ans;
        return 0.5 * (2.0 - $erfc);
    }
}
