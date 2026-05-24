<?php

namespace NeuralPHP\Optimizers;

class SGD
{
    private $learning_rate;

    public function __construct(float $learning_rate = 0.01)
    {
        $this->learning_rate = $learning_rate;
    }

    public function update(array &$weights, array &$biases, array $dWeights, array $dBiases)
    {
        $input_size = count($weights);
        $output_size = count($biases);

        for ($i = 0; $i < $input_size; $i++) {
            for ($j = 0; $j < $output_size; $j++) {
                $weights[$i][$j] -= $this->learning_rate * $dWeights[$i][$j];
            }
        }

        for ($j = 0; $j < $output_size; $j++) {
            $biases[$j] -= $this->learning_rate * $dBiases[$j];
        }
    }
}
