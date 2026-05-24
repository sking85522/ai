<?php

namespace DatasetPHP\Utils;

class Splitter
{
    /**
     * Splits arrays or matrices into random train and test subsets.
     */
    public static function train_test_split(array $X, array $y, float $test_size = 0.25): array
    {
        $n = count($X);
        if ($n !== count($y)) {
            throw new \InvalidArgumentException("X and y must have the same length.");
        }

        // Create an array of indices
        $indices = range(0, $n - 1);

        // Shuffle indices
        shuffle($indices);

        // Determine split index
        $split_idx = (int) round($n * (1 - $test_size));

        $X_train = [];
        $y_train = [];
        $X_test = [];
        $y_test = [];

        for ($i = 0; $i < $n; $i++) {
            $idx = $indices[$i];
            if ($i < $split_idx) {
                $X_train[] = $X[$idx];
                $y_train[] = $y[$idx];
            } else {
                $X_test[] = $X[$idx];
                $y_test[] = $y[$idx];
            }
        }

        return [$X_train, $X_test, $y_train, $y_test];
    }
}
