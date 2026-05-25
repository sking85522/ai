<?php

namespace MLPHP\PreProcessing;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class StandardScaler
{
    private $mean = [];
    private $scale = [];

    public function fit($X)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $n_samples = count($X_data);
        $n_features = count($X_data[0]);

        $this->mean = array_fill(0, $n_features, 0.0);
        $this->scale = array_fill(0, $n_features, 0.0);

        // Compute Mean
        for ($i = 0; $i < $n_samples; $i++) {
            for ($j = 0; $j < $n_features; $j++) {
                $this->mean[$j] += $X_data[$i][$j];
            }
        }
        for ($j = 0; $j < $n_features; $j++) {
            $this->mean[$j] /= $n_samples;
        }

        // Compute Variance (Scale)
        for ($i = 0; $i < $n_samples; $i++) {
            for ($j = 0; $j < $n_features; $j++) {
                $this->scale[$j] += pow($X_data[$i][$j] - $this->mean[$j], 2);
            }
        }
        for ($j = 0; $j < $n_features; $j++) {
            $this->scale[$j] = sqrt($this->scale[$j] / $n_samples);
            if ($this->scale[$j] == 0) $this->scale[$j] = 1.0; // Avoid division by zero
        }

        return $this;
    }

    public function transform($X)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $n_samples = count($X_data);
        $n_features = count($X_data[0]);
        $X_scaled = [];

        for ($i = 0; $i < $n_samples; $i++) {
            $row = [];
            for ($j = 0; $j < $n_features; $j++) {
                $row[] = ($X_data[$i][$j] - $this->mean[$j]) / $this->scale[$j];
            }
            $X_scaled[] = $row;
        }

        return np::array($X_scaled);
    }

    public function fit_transform($X)
    {
        return $this->fit($X)->transform($X);
    }
}
