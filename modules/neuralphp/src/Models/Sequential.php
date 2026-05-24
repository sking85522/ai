<?php

namespace NeuralPHP\Models;

use NeuralPHP\Layers\Dense;
use NeuralPHP\NeuralPHP;

class Sequential
{
    private $layers = [];
    private $loss;
    private $optimizer;

    public function add(object $layer)
    {
        $this->layers[] = $layer;
    }

    public function compile(string $optimizer, float $lr = 0.01, string $loss = 'mse')
    {
        $this->optimizer = NeuralPHP::getOptimizer($optimizer, $lr);
        $this->loss = NeuralPHP::getLoss($loss);
    }

    public function fit(array $X_train, array $y_train, int $epochs = 1000)
    {
        for ($epoch = 0; $epoch < $epochs; $epoch++) {
            $total_error = 0;

            for ($i = 0; $i < count($X_train); $i++) {
                // Forward pass
                $inputs = $X_train[$i];
                foreach ($this->layers as $layer) {
                    $inputs = $layer->forward($inputs);
                }

                // Calculate loss
                $predictions = $inputs;
                $targets = $y_train[$i];
                $total_error += $this->loss->calculate($targets, $predictions);

                // Backward pass
                $dOutputs = $this->loss->derivative($targets, $predictions);
                for ($l = count($this->layers) - 1; $l >= 0; $l--) {
                    $dOutputs = $this->layers[$l]->backward($dOutputs, $this->optimizer);
                }
            }

            if ($epoch % 1000 == 0) {
                // Uncomment to see progress
                // echo "Epoch $epoch: Error = " . ($total_error / count($X_train)) . "\n";
            }
        }
    }

    public function predict(array $X_test): array
    {
        $predictions = [];
        foreach ($X_test as $inputs) {
            foreach ($this->layers as $layer) {
                $inputs = $layer->forward($inputs);
            }
            $predictions[] = $inputs;
        }
        return $predictions;
    }
}
