<?php
namespace NeuralPHP\Activations;

class Tanh {
    public function forward(array $x): array {
        return array_map('tanh', $x);
    }
    public function derivative(array $x): array {
        return array_map(fn($v) => 1 - tanh($v) ** 2, $x);
    }
}
