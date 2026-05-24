<?php
namespace Core\Evaluation\ConfusionMatrix;

class ConfusionMatrix {
    /**
     * Generates a confusion matrix from true and predicted values.
     */
    public function generate(array $yTrue, array $yPred): array {
        $labels = array_unique(array_merge($yTrue, $yPred));
        sort($labels);
        
        $matrix = [];
        foreach ($labels as $trueLabel) {
            $matrix[$trueLabel] = array_fill_keys($labels, 0);
        }

        foreach ($yTrue as $i => $true) {
            $pred = $yPred[$i] ?? null;
            if ($pred !== null && isset($matrix[$true][$pred])) {
                $matrix[$true][$pred]++;
            }
        }

        return $matrix;
    }
}
