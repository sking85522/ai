<?php
namespace NeuralPHP\Activations;

class LeakyReLU {
    private $alpha;
    public function __construct(float $alpha = 0.01) { $this->alpha = $alpha; }

    public function forward(array $x): array {
        return array_map(fn($v) => $v > 0 ? $v : $this->alpha * $v, $x);
    }
    public function derivative(array $x): array {
        return array_map(fn($v) => $v > 0 ? 1.0 : $this->alpha, $x);
    }
}
