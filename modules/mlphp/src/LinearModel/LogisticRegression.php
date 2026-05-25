<?php

namespace MLPHP\LinearModel;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class LogisticRegression
{
    private $coef = null; // Weights
    private $intercept = null; // Bias
    private $learning_rate;
    private $max_iter;

    public function __construct(float $learning_rate = 0.01, int $max_iter = 1000)
    {
        $this->learning_rate = $learning_rate;
        $this->max_iter = $max_iter;
    }

    private function sigmoid(float $z): float
    {
        return 1.0 / (1.0 + exp(-$z));
    }

    /**
     * Fit logistic regression model using Gradient Descent.
     */
    public function fit($X, $y)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $y_data = ($y instanceof NDArray) ? $y->getData() : $y;

        $n_samples = count($X_data);
        $n_features = count($X_data[0]);

        // Initialize weights to zero
        $this->coef = array_fill(0, $n_features, 0.0);
        $this->intercept = 0.0;

        for ($iter = 0; $iter < $this->max_iter; $iter++) {
            $dw = array_fill(0, $n_features, 0.0);
            $db = 0.0;

            for ($i = 0; $i < $n_samples; $i++) {
                // Linear combination
                $linear_model = $this->intercept;
                for ($j = 0; $j < $n_features; $j++) {
                    $linear_model += $this->coef[$j] * $X_data[$i][$j];
                }

                $y_predicted = $this->sigmoid($linear_model);

                // Gradients
                $error = $y_predicted - $y_data[$i];
                $db += $error;
                for ($j = 0; $j < $n_features; $j++) {
                    $dw[$j] += $error * $X_data[$i][$j];
                }
            }

            // Update parameters
            $this->intercept -= $this->learning_rate * ($db / $n_samples);
            for ($j = 0; $j < $n_features; $j++) {
                $this->coef[$j] -= $this->learning_rate * ($dw[$j] / $n_samples);
            }
        }

        return $this;
    }

    /**
     * Predict class labels for samples in X.
     */
    public function predict($X)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $n_samples = count($X_data);
        $predictions = [];

        for ($i = 0; $i < $n_samples; $i++) {
            $linear_model = $this->intercept;
            for ($j = 0; $j < count($this->coef); $j++) {
                $linear_model += $this->coef[$j] * $X_data[$i][$j];
            }
            $y_predicted = $this->sigmoid($linear_model);
            $predictions[] = ($y_predicted >= 0.5) ? 1 : 0;
        }

        return np::array($predictions);
    }
}
