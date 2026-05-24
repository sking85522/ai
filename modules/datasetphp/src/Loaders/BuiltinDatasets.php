<?php
namespace DatasetPHP\Loaders;

/**
 * Built-in toy datasets for testing ML algorithms.
 */
class BuiltinDatasets
{
    /**
     * Fisher's Iris dataset — 150 samples, 4 features, 3 classes.
     * Features: sepal_length, sepal_width, petal_length, petal_width
     * Classes: 0=setosa, 1=versicolor, 2=virginica
     */
    public static function iris(): array
    {
        $X = [
            // Setosa (class 0) — 15 representative samples
            [5.1,3.5,1.4,0.2],[4.9,3.0,1.4,0.2],[4.7,3.2,1.3,0.2],[4.6,3.1,1.5,0.2],[5.0,3.6,1.4,0.2],
            [5.4,3.9,1.7,0.4],[4.6,3.4,1.4,0.3],[5.0,3.4,1.5,0.2],[4.4,2.9,1.4,0.2],[4.9,3.1,1.5,0.1],
            [5.4,3.7,1.5,0.2],[4.8,3.4,1.6,0.2],[4.8,3.0,1.4,0.1],[4.3,3.0,1.1,0.1],[5.8,4.0,1.2,0.2],
            // Versicolor (class 1)
            [7.0,3.2,4.7,1.4],[6.4,3.2,4.5,1.5],[6.9,3.1,4.9,1.5],[5.5,2.3,4.0,1.3],[6.5,2.8,4.6,1.5],
            [5.7,2.8,4.5,1.3],[6.3,3.3,4.7,1.6],[4.9,2.4,3.3,1.0],[6.6,2.9,4.6,1.3],[5.2,2.7,3.9,1.4],
            [5.0,2.0,3.5,1.0],[5.9,3.0,4.2,1.5],[6.0,2.2,4.0,1.0],[6.1,2.9,4.7,1.4],[5.6,2.9,3.6,1.3],
            // Virginica (class 2)
            [6.3,3.3,6.0,2.5],[5.8,2.7,5.1,1.9],[7.1,3.0,5.9,2.1],[6.3,2.9,5.6,1.8],[6.5,3.0,5.8,2.2],
            [7.6,3.0,6.6,2.1],[4.9,2.5,4.5,1.7],[7.3,2.9,6.3,1.8],[6.7,2.5,5.8,1.8],[7.2,3.6,6.1,2.5],
            [6.5,3.2,5.1,2.0],[6.4,2.7,5.3,1.9],[6.8,3.0,5.5,2.1],[5.7,2.5,5.0,2.0],[5.8,2.8,5.1,2.4],
        ];

        $y = array_merge(array_fill(0, 15, 0), array_fill(0, 15, 1), array_fill(0, 15, 2));

        return [
            'X' => $X,
            'y' => $y,
            'feature_names' => ['sepal_length', 'sepal_width', 'petal_length', 'petal_width'],
            'target_names' => ['setosa', 'versicolor', 'virginica'],
            'description' => "Fisher's Iris dataset — 45 samples (15 per class), 4 features",
        ];
    }

    /**
     * XOR dataset — Classic non-linearly separable problem.
     */
    public static function xor(): array
    {
        return [
            'X' => [[0,0],[0,1],[1,0],[1,1]],
            'y' => [0, 1, 1, 0],
            'description' => 'XOR gate — requires non-linear decision boundary',
        ];
    }

    /**
     * Simple linear regression dataset.
     */
    public static function linear(): array
    {
        $X = []; $y = [];
        for ($i = 0; $i < 50; $i++) {
            $x = $i * 0.2;
            $X[] = [$x];
            $y[] = 2.5 * $x + 3.0 + (mt_rand(-100, 100) / 100); // y = 2.5x + 3 + noise
        }
        return [
            'X' => $X,
            'y' => $y,
            'description' => 'Linear dataset: y ≈ 2.5x + 3 with noise',
        ];
    }
}
