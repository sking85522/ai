<?php
namespace Core\DL;

require_once __DIR__ . '/Layer.php';
require_once __DIR__ . '/Activation.php';
require_once __DIR__ . '/Backpropagation.php';

class NeuralNetwork {
    private array $layers = [];
    private Backpropagation $optimizer;

    public function __construct(float $learningRate = 0.1) {
        $this->optimizer = new Backpropagation($learningRate);
    }

    public function addLayer(int $inputSize, int $outputSize, string $activation = 'sigmoid'): void {
        $this->layers[] = new Layer($inputSize, $outputSize, $activation);
    }

    /**
     * Propagates input through all layers.
     */
    public function predict(array $input): array {
        $current = $input;
        foreach ($this->layers as $layer) {
            $current = $layer->forward($current);
        }
        return $current;
    }

    /**
     * Trains the network on a single sample using backprop.
     */
    public function trainSample(array $input, array $target): void {
        // 1. Forward pass
        $output = $this->predict($input);

        // 2. Backward pass (Calculate deltas and update)
        // Starting from the output layer
        $lastIdx = count($this->layers) - 1;
        $deltas = $this->optimizer->calculateOutputDeltas($output, $target, $this->layers[$lastIdx]->activation);
        
        // Update output layer
        $this->optimizer->update($this->layers[$lastIdx], $deltas);

        // Backpropagate to hidden layers
        for ($i = $lastIdx - 1; $i >= 0; $i--) {
            $currentLayer = $this->layers[$i];
            $nextLayer = $this->layers[$i+1];
            
            $newDeltas = [];
            foreach ($currentLayer->lastOutput as $j => $o) {
                // Calculate gradient based on next layer's deltas
                $error = 0;
                foreach ($nextLayer->weights as $k => $weightsRow) {
                    $error += $deltas[$k] * $weightsRow[$j];
                }
                
                $derivative = ($currentLayer->activation === 'sigmoid') ? $o * (1 - $o) : 1;
                $newDeltas[$j] = $error * $derivative;
            }
            
            $deltas = $newDeltas;
            $this->optimizer->update($currentLayer, $deltas);
        }
    }

    public function train(array $X, array $y, int $epochs = 1000): void {
        for ($e = 0; $e < $epochs; $e++) {
            foreach ($X as $i => $input) {
                $this->trainSample($input, $y[$i]);
            }
        }
    }

    private Layer $hiddenLayer;
    private Layer $outputLayer;

    public function __construct(int $inputSize = 8, int $hiddenSize = 6, int $outputSize = 3)
    {
        $this->hiddenLayer = new Layer($inputSize, $hiddenSize, 'relu');
        $this->outputLayer = new Layer($hiddenSize, $outputSize, 'sigmoid');
    }

    public function forward(array $inputs): array
    {
        $hidden = $this->hiddenLayer->forward($inputs);
        return $this->outputLayer->forward($hidden);
    }

    public function score(array $inputs): float
    {
        $outputs = $this->forward($inputs);
        return round(array_sum($outputs) / max(1, count($outputs)), 4);
    }

    public function tune(float $delta): void
    {
        $this->hiddenLayer->adjust($delta);
        $this->outputLayer->adjust($delta / 2);
    }
}
