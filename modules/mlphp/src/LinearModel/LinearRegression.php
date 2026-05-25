<?php

namespace MLPHP\LinearModel;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class LinearRegression
{
    private $coef = null; // Weights
    private $intercept = null; // Bias
    private $fit_intercept;

    public function __construct(bool $fit_intercept = true)
    {
        $this->fit_intercept = $fit_intercept;
    }

    /**
     * Fit linear model using Ordinary Least Squares.
     * X must be an array of samples (2D array).
     * y must be an array of targets (1D array).
     */
    public function fit($X, $y)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $y_data = ($y instanceof NDArray) ? $y->getData() : $y;

        $n_samples = count($X_data);
        $n_features = count($X_data[0]);

        if ($this->fit_intercept) {
            // Add a column of ones to X for the intercept term
            for ($i = 0; $i < $n_samples; $i++) {
                array_unshift($X_data[$i], 1.0);
            }
            $n_features++;
        }

        // Normal Equation: theta = (X^T * X)^-1 * X^T * y
        // First compute X^T (transpose of X)
        $X_T = [];
        for ($i = 0; $i < $n_features; $i++) {
            $X_T[$i] = [];
            for ($j = 0; $j < $n_samples; $j++) {
                $X_T[$i][$j] = $X_data[$j][$i];
            }
        }

        // Compute X^T * X
        $XTX = [];
        for ($i = 0; $i < $n_features; $i++) {
            $XTX[$i] = [];
            for ($j = 0; $j < $n_features; $j++) {
                $sum = 0;
                for ($k = 0; $k < $n_samples; $k++) {
                    $sum += $X_T[$i][$k] * $X_data[$k][$j];
                }
                $XTX[$i][$j] = $sum;
            }
        }

        // Compute Inverse of (X^T * X)
        // Since we already built linalg_inv in SciPHP, we can use it, but to keep MLPHP decoupled
        // from SciPHP (or relying only on NumPHP), let's implement a quick Gauss-Jordan inversion
        $XTX_inv = $this->invertMatrix($XTX);

        // Compute X^T * y
        $XTy = [];
        for ($i = 0; $i < $n_features; $i++) {
            $sum = 0;
            for ($k = 0; $k < $n_samples; $k++) {
                $sum += $X_T[$i][$k] * $y_data[$k];
            }
            $XTy[$i] = $sum;
        }

        // Compute theta = XTX_inv * XTy
        $theta = [];
        for ($i = 0; $i < $n_features; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n_features; $j++) {
                $sum += $XTX_inv[$i][$j] * $XTy[$j];
            }
            $theta[$i] = $sum;
        }

        if ($this->fit_intercept) {
            $this->intercept = $theta[0];
            $this->coef = array_slice($theta, 1);
        } else {
            $this->intercept = 0.0;
            $this->coef = $theta;
        }

        return $this;
    }

    /**
     * Predict using the linear model.
     */
    public function predict($X)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $n_samples = count($X_data);
        $predictions = [];

        for ($i = 0; $i < $n_samples; $i++) {
            $pred = $this->intercept;
            for ($j = 0; $j < count($this->coef); $j++) {
                $pred += $this->coef[$j] * $X_data[$i][$j];
            }
            $predictions[] = $pred;
        }

        return np::array($predictions);
    }

    public function getCoef()
    {
        return $this->coef;
    }

    public function getIntercept()
    {
        return $this->intercept;
    }

    private function invertMatrix(array $mat)
    {
        $n = count($mat);
        $aug = [];
        for ($i = 0; $i < $n; $i++) {
            $aug[$i] = $mat[$i];
            for ($j = 0; $j < $n; $j++) {
                $aug[$i][] = ($i === $j) ? 1.0 : 0.0;
            }
        }
        for ($i = 0; $i < $n; $i++) {
            if ($aug[$i][$i] == 0) {
                // Find non-zero pivot
                $swap = false;
                for ($k = $i + 1; $k < $n; $k++) {
                    if ($aug[$k][$i] != 0) {
                        $tmp = $aug[$i]; $aug[$i] = $aug[$k]; $aug[$k] = $tmp;
                        $swap = true; break;
                    }
                }
                if (!$swap) throw new \Exception("Singular matrix, cannot compute OLS analytically.");
            }
            $pivot = $aug[$i][$i];
            for ($j = 0; $j < 2 * $n; $j++) $aug[$i][$j] /= $pivot;
            for ($j = 0; $j < $n; $j++) {
                if ($i !== $j) {
                    $factor = $aug[$j][$i];
                    for ($k = 0; $k < 2 * $n; $k++) $aug[$j][$k] -= $factor * $aug[$i][$k];
                }
            }
        }
        $inv = [];
        for ($i = 0; $i < $n; $i++) $inv[] = array_slice($aug[$i], $n);
        return $inv;
    }
}
