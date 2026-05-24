<?php

namespace SciPHP\Optimization;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class Minimize
{
    /**
     * Minimizes a scalar function of one or more variables using Gradient Descent.
     * This is a basic implementation of scipy.optimize.minimize using 'gradient descent'.
     *
     * @param callable $fun The objective function to be minimized.
     * @param mixed $x0 Initial guess. Array of real elements of size (n,),
     *                  where 'n' is the number of independent variables.
     * @param float $learning_rate Step size.
     * @param int $max_iter Maximum number of iterations to perform.
     * @param float $tolerance Tolerance for termination.
     * @return array Result array containing 'x' (solution array) and 'success' (boolean).
     */
    public static function gradient_descent(callable $fun, $x0, float $learning_rate = 0.01, int $max_iter = 1000, float $tolerance = 1e-6): array
    {
        $x = is_array($x0) ? $x0 : [$x0];
        $n = count($x);
        $h = 1e-5; // Step size for numerical gradient

        for ($iter = 0; $iter < $max_iter; $iter++) {
            $grad = [];
            // Compute numerical gradient
            for ($i = 0; $i < $n; $i++) {
                $x_plus_h = $x;
                $x_plus_h[$i] += $h;
                $x_minus_h = $x;
                $x_minus_h[$i] -= $h;

                $f_plus = $fun($x_plus_h);
                $f_minus = $fun($x_minus_h);

                $grad[$i] = ($f_plus - $f_minus) / (2 * $h);
            }

            // Update x using gradient
            $max_grad = 0;
            $x_new = $x;
            for ($i = 0; $i < $n; $i++) {
                $x_new[$i] = $x[$i] - $learning_rate * $grad[$i];
                $max_grad = max($max_grad, abs($grad[$i]));
            }

            // Check for convergence
            if ($max_grad < $tolerance) {
                return [
                    'x' => $x_new,
                    'success' => true,
                    'nit' => $iter,
                    'message' => 'Optimization terminated successfully.'
                ];
            }

            $x = $x_new;
        }

        return [
            'x' => $x,
            'success' => false,
            'nit' => $max_iter,
            'message' => 'Maximum number of iterations exceeded.'
        ];
    }
}
