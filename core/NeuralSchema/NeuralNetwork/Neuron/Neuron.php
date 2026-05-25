<?php
namespace Core\NeuralSchema\NeuralNetwork\Neuron;

class Neuron {
    private float $bias;
    private array $weights = [];
    private string $activation;

    public function __construct(int $inputSize, string $activation = 'relu') {
        $this->bias = (float)rand() / (float)getrandmax() - 0.5;
        $this->activation = $activation;
        
        for ($i = 0; $i < $inputSize; $i++) {
            $this->weights[] = (float)rand() / (float)getrandmax() - 0.5;
        }
    }

    public function activate(array $inputs): float {
        $sum = $this->bias;
        foreach ($inputs as $i => $input) {
            $sum += $input * ($this->weights[$i] ?? 0);
        }
        return $sum; // Raw sum, activation applied at layer level
    }

    public function getWeights(): array {
        return $this->weights;
    }
}
