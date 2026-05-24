<?php
namespace NeuralPHP\Activations;

class Softmax {
    public function forward(array $x): array {
        $maxVal = max($x);
        $exps = array_map(fn($v) => exp($v - $maxVal), $x);
        $sumExps = array_sum($exps);
        return array_map(fn($v) => $v / $sumExps, $exps);
    }
    public function derivative(array $x): array {
        $s = $this->forward($x);
        // Simplified: returns softmax output (used in conjunction with cross-entropy loss)
        return $s;
    }
}
