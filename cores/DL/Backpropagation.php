<?php
namespace Core\DL;

class Backpropagation {
    private float $learningRate;

    public function __construct(float $learningRate = 0.01) {
        $this->learningRate = $learningRate;
    }

    /**
     * Updates the weights and biases of a layer based on calculated gradients.
     */
    public function update(Layer &$layer, array $deltas): void {
        foreach ($layer->weights as $i => &$weightsRow) {
            foreach ($weightsRow as $j => &$weight) {
                // Stochastic Gradient Descent
                $weight -= $this->learningRate * $deltas[$i] * $layer->lastInput[$j];
            }
            $layer->biases[$i] -= $this->learningRate * $deltas[$i];
        }
    }

    /**
     * Calculates deltas for the output layer.
     */
    public function calculateOutputDeltas(array $output, array $target, string $activation): array {
        $deltas = [];
        foreach ($output as $i => $o) {
            $error = $o - $target[$i];
            
            $derivative = 1.0;
            if ($activation === 'sigmoid') {
                $derivative = $o * (1 - $o);
            } elseif ($activation === 'relu') {
                $derivative = $o > 0 ? 1 : 0;
            }
            
            $deltas[$i] = $error * $derivative;
        }
        return $deltas;
    }

    public function adjust(NeuralNetwork $network, float $expected, float $actual, float $learningRate = 0.05): float
    {
        $error = $expected - $actual;
        $delta = $error * $learningRate;
        $network->tune($delta);
        return $error;
    }
}