<?php
namespace Core\NeuralSchema\NeuralNetwork\Layers\InputLayer;

class InputLayer {
    private int $size;

    public function __construct(int $size) {
        $this->size = $size;
    }

    public function forward(array $inputs): array {
        return $inputs; // Input layer just passes data
    }
}
