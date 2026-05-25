<?php
namespace Core\DL\Layers;

/**
 * HRITIK AI - MODULAR DENSE LAYER
 * High-performance fully connected neural layer with Xavier initialization.
 */
class DenseLayer {
    
    public array $weights = [];
    public array $biases = [];
    private int $inputSize;
    private int $outputSize;

    public function __construct(int $inputSize, int $outputSize) {
        $this->inputSize = $inputSize;
        $this->outputSize = $outputSize;
        $this->initializeWeights();
    }

    /**
     * Xavier/Glorot Initialization for stable training.
     */
    private function initializeWeights(): void {
        $limit = sqrt(6 / ($this->inputSize + $this->outputSize));
        for ($i = 0; $i < $this->outputSize; $i++) {
            $this->biases[$i] = 0.0;
            for ($j = 0; $j < $this->inputSize; $j++) {
                $this->weights[$i][$j] = (rand(-1000, 1000) / 1000) * $limit;
            }
        }
    }

    /**
     * Forward pass through the layer.
     */
    public function forward(array $inputs): array {
        $outputs = [];
        foreach ($this->weights as $i => $nodeWeights) {
            $sum = $this->biases[$i];
            foreach ($nodeWeights as $j => $w) {
                $sum += $w * ($inputs[$j] ?? 0);
            }
            $outputs[$i] = $sum;
        }
        return $outputs;
    }
}
