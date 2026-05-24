<?php

namespace SciPHP\Integration;

class Quad
{
    /**
     * Compute a definite integral using Simpson's rule.
     * This approximates scipy.integrate.quad.
     *
     * @param callable $func A PHP function or method to integrate.
     * @param float $a Lower limit of integration.
     * @param float $b Upper limit of integration.
     * @param int $n Number of subintervals (must be even).
     * @return array Result array containing 'integral' (float) and 'error' (float, estimated).
     */
    public static function integrate(callable $func, float $a, float $b, int $n = 1000): array
    {
        if ($n % 2 != 0) {
            $n++; // Ensure n is even
        }

        $h = ($b - $a) / $n;
        $sum = $func($a) + $func($b);

        for ($i = 1; $i < $n; $i += 2) {
            $sum += 4 * $func($a + $i * $h);
        }
        for ($i = 2; $i < $n - 1; $i += 2) {
            $sum += 2 * $func($a + $i * $h);
        }

        $integral = $sum * $h / 3.0;

        // Basic error estimation based on the difference between N and N/2 steps
        $n_half = $n / 2;
        $h_half = ($b - $a) / $n_half;
        $sum_half = $func($a) + $func($b);
        for ($i = 1; $i < $n_half; $i += 2) {
            $sum_half += 4 * $func($a + $i * $h_half);
        }
        for ($i = 2; $i < $n_half - 1; $i += 2) {
            $sum_half += 2 * $func($a + $i * $h_half);
        }
        $integral_half = $sum_half * $h_half / 3.0;

        $error = abs($integral - $integral_half) / 15.0; // Richardson extrapolation error estimate

        return [
            'integral' => $integral,
            'error' => $error
        ];
    }
}
