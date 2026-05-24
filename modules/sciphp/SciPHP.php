<?php

namespace SciPHP;

use SciPHP\Stats\Norm;
use NumPHP\Core\NDArray;
use SciPHP\Optimization\Minimize;
use SciPHP\Optimization\RootFinding;
use SciPHP\Integration\Quad;
use SciPHP\Interpolation\Linear;
use SciPHP\LinearAlgebra\Linalg;
use SciPHP\LinearAlgebra\AdvancedLinalg;
use SciPHP\Statistics\NormalDist;
use SciPHP\Signal\Convolve;

class SciPHP
{
    /**
     * A normal continuous random variable.
     *
     * @return Norm
     */
    public static function norm(): Norm
    {
        return new Norm();
    }

    /**
     * Minimization of scalar function of one or more variables.
     *
     * @param callable $fun The objective function to be minimized.
     * @param mixed $x0 Initial guess.
     * @return array
     */
    public static function optimize_minimize(callable $fun, $x0): array
    {
        return Minimize::gradient_descent($fun, $x0);
    }

    /**
     * Find a zero of a real or complex function using the Newton-Raphson method.
     *
     * @param callable $func
     * @param float $x0
     * @param callable|null $fprime
     * @return float
     */
    public static function optimize_newton(callable $func, float $x0, ?callable $fprime = null): float
    {
        return RootFinding::newton($func, $x0, $fprime);
    }

    /**
     * Compute a definite integral.
     *
     * @param callable $func A PHP function or method to integrate.
     * @param float $a Lower limit of integration.
     * @param float $b Upper limit of integration.
     * @return array
     */
    public static function integrate_quad(callable $func, float $a, float $b): array
    {
        return Quad::integrate($func, $a, $b);
    }

    /**
     * Interpolate a 1-D function.
     *
     * @param mixed $x A 1-D array of real values.
     * @param mixed $y A 1-D array of real values.
     * @return callable
     */
    public static function interpolate_interp1d($x, $y): callable
    {
        return Linear::interp1d($x, $y);
    }

    /**
     * Solve a linear matrix equation, or system of linear scalar equations.
     *
     * @param NDArray $a Coefficient matrix.
     * @param NDArray $b Ordinate or "dependent variable" values.
     * @return NDArray Solution to the system a x = b.
     */
    public static function linalg_solve(NDArray $a, NDArray $b): NDArray
    {
        return Linalg::solve($a, $b);
    }

    /**
     * Compute the determinant of an array.
     *
     * @param NDArray $a
     * @return float
     */
    public static function linalg_det(NDArray $a): float
    {
        return AdvancedLinalg::det($a);
    }

    /**
     * Compute the (multiplicative) inverse of a matrix.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function linalg_inv(NDArray $a): NDArray
    {
        return AdvancedLinalg::inv($a);
    }

    /**
     * Normal probability density function.
     *
     * @param float|NDArray|array $x
     * @param float $loc
     * @param float $scale
     * @return float|NDArray
     */
    public static function stats_norm_pdf($x, float $loc = 0.0, float $scale = 1.0)
    {
        return NormalDist::pdf($x, $loc, $scale);
    }

    /**
     * Normal cumulative distribution function.
     *
     * @param float|NDArray|array $x
     * @param float $loc
     * @param float $scale
     * @return float|NDArray
     */
    public static function stats_norm_cdf($x, float $loc = 0.0, float $scale = 1.0)
    {
        return NormalDist::cdf($x, $loc, $scale);
    }

    /**
     * Convolve two N-dimensional arrays. (Currently 1D)
     *
     * @param mixed $in1
     * @param mixed $in2
     * @param string $mode
     * @return NDArray
     */
    public static function signal_convolve($in1, $in2, string $mode = 'full'): NDArray
    {
        return Convolve::convolve1d($in1, $in2, $mode);
    }
}
