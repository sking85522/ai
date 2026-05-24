<?php
namespace NeuralPHP\Losses;

class BinaryCrossEntropy {
    public function calculate(array $target, array $predicted): float {
        $loss = 0.0;
        $epsilon = 1e-15;
        $n = count($target);
        for ($i = 0; $i < $n; $i++) {
            $p = max($epsilon, min(1 - $epsilon, $predicted[$i]));
            $loss -= $target[$i] * log($p) + (1 - $target[$i]) * log(1 - $p);
        }
        return $loss / $n;
    }

    public function derivative(array $target, array $predicted): array {
        $grad = [];
        $epsilon = 1e-15;
        for ($i = 0; $i < count($target); $i++) {
            $p = max($epsilon, min(1 - $epsilon, $predicted[$i]));
            $grad[] = (-$target[$i] / $p + (1 - $target[$i]) / (1 - $p));
        }
        return $grad;
    }
}
