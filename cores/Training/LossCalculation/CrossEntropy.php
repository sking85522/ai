<?php
namespace Core\Training\LossCalculation;

class CrossEntropy {
    /**
     * Calculates categorical cross-entropy loss.
     */
    public function calculate(array $yTrue, array $yPred): float {
        $loss = 0.0;
        foreach ($yTrue as $i => $val) {
            $probability = max(1e-15, (float)($yPred[$i] ?? 0.0));
            $loss -= (float)$val * log($probability);
        }

        return $loss / max(1, count($yTrue));
    }
}
