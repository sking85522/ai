<?php
namespace NeuralPHP\Optimizers;

class RMSprop {
    private $lr;
    private $decay;
    private $epsilon;
    private $cache = [];

    public function __construct(float $lr = 0.001, float $decay = 0.9, float $epsilon = 1e-8) {
        $this->lr = $lr;
        $this->decay = $decay;
        $this->epsilon = $epsilon;
    }

    public function update(array &$weights, array &$biases, array $dWeights, array $dBiases): void {
        $input_size = count($weights);
        $output_size = count($biases);

        if (!isset($this->cache['weights'])) {
            $this->cache['weights'] = array_fill(0, $input_size, array_fill(0, $output_size, 0.0));
            $this->cache['biases'] = array_fill(0, $output_size, 0.0);
        }

        // Update weights
        for ($i = 0; $i < $input_size; $i++) {
            for ($j = 0; $j < $output_size; $j++) {
                $this->cache['weights'][$i][$j] = $this->decay * $this->cache['weights'][$i][$j] + (1 - $this->decay) * ($dWeights[$i][$j] ** 2);
                $weights[$i][$j] -= $this->lr * $dWeights[$i][$j] / (sqrt($this->cache['weights'][$i][$j]) + $this->epsilon);
            }
        }

        // Update biases
        for ($j = 0; $j < $output_size; $j++) {
            $this->cache['biases'][$j] = $this->decay * $this->cache['biases'][$j] + (1 - $this->decay) * ($dBiases[$j] ** 2);
            $biases[$j] -= $this->lr * $dBiases[$j] / (sqrt($this->cache['biases'][$j]) + $this->epsilon);
        }
    }
}
