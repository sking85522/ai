<?php

namespace SciPHP\Optimization;

class RootFinding
{
    /**
     * Find a zero of a real or complex function using the Newton-Raphson (or secant or Halley's) method.
     * This approximates scipy.optimize.newton.
     *
     * @param callable $func The function whose zero is wanted. It must be a function of a single variable.
     * @param float $x0 An initial estimate of the zero that should be somewhere near the actual zero.
     * @param callable|null $fprime The derivative of the function when available and convenient. If it is null, then the secant method is used.
     * @param float $tol The allowable error of the zero value.
     * @param int $maxiter Maximum number of iterations.
     * @return float Estimated location where function is zero.
     */
    public static function newton(callable $func, float $x0, ?callable $fprime = null, float $tol = 1.48e-8, int $maxiter = 50): float
    {
        $p0 = $x0;

        if ($fprime !== null) {
            // Newton-Raphson method
            for ($iter = 0; $iter < $maxiter; $iter++) {
                $fval = $func($p0);
                if ($fval == 0.0) {
                    return $p0;
                }

                $fder = $fprime($p0);
                if ($fder == 0.0) {
                    throw new \RuntimeException("Derivative was zero.");
                }

                $p = $p0 - $fval / $fder;

                if (abs($p - $p0) < $tol) {
                    return $p;
                }
                $p0 = $p;
            }
        } else {
            // Secant method
            $p1 = $x0 * 1.0001;
            if ($p1 == $x0) {
                $p1 = $x0 + 0.0001;
            }

            $q0 = $func($p0);
            $q1 = $func($p1);

            if (abs($q1) < abs($q0)) {
                $tmp = $p0; $p0 = $p1; $p1 = $tmp;
                $tmp = $q0; $q0 = $q1; $q1 = $tmp;
            }

            for ($iter = 0; $iter < $maxiter; $iter++) {
                if ($q0 == $q1) {
                    if ($p0 != $p1) {
                        throw new \RuntimeException("Tolerance reached but function values are not zero.");
                    } else {
                        return ($p0 + $p1) / 2.0;
                    }
                }

                $p = $p1 - $q1 * ($p1 - $p0) / ($q1 - $q0);

                if (abs($p - $p1) < $tol) {
                    return $p;
                }

                $p0 = $p1;
                $q0 = $q1;
                $p1 = $p;
                $q1 = $func($p1);
            }
        }

        throw new \RuntimeException("Failed to converge after $maxiter iterations.");
    }
}
