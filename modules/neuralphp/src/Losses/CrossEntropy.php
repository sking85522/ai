<?php
namespace NeuralPHP\Losses;

class CrossEntropy {
    /**
     * Categorical cross-entropy loss.
     */
    public function calculate(array $target, array $predicted): float {
        $loss = 0.0;
        $epsilon = 1e-15;
        for ($i = 0; $i < count($target); $i++) {
            $p = max($epsilon, min(1 - $epsilon, $predicted[$i] ?? 0.0));
            $loss -= $target[$i] * log($p);
        }
        return $loss;
    }

    public function derivative(array $target, array $predicted): array {
        $grad = [];
        $epsilon = 1e-15;
        for ($i = 0; $i < count($target); $i++) {
            $p = max($epsilon, min(1 - $epsilon, $predicted[$i] ?? 0.0));
            $grad[] = -$target[$i] / $p;
        }
        return $grad;
    }
}
