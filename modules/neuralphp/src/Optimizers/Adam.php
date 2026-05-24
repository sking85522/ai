<?php
namespace NeuralPHP\Optimizers;

/**
 * Adam Optimizer — Adaptive Moment Estimation.
 * Combines RMSprop + Momentum for faster convergence.
 */
class Adam {
    private $lr;
    private $beta1;
    private $beta2;
    private $epsilon;
    private $m = []; // First moment (mean)
    private $v = []; // Second moment (variance)
    private $t = 0;

    public function __construct(float $lr = 0.001, float $beta1 = 0.9, float $beta2 = 0.999, float $epsilon = 1e-8) {
        $this->lr = $lr;
        $this->beta1 = $beta1;
        $this->beta2 = $beta2;
        $this->epsilon = $epsilon;
    }

    public function update(array &$weights, array &$biases, array $dWeights, array $dBiases): void {
        $this->t++;

        $input_size = count($weights);
        $output_size = count($biases);

        // Ensure per-layer caching by using a combination of input_size and output_size or checking each layer dynamically.
        // Better yet, initialize dynamically if missing
        if (!isset($this->m['weights'])) {
            $this->m['weights'] = [];
            $this->v['weights'] = [];
            $this->m['biases'] = [];
            $this->v['biases'] = [];
        }

        // Dynamically build arrays if missing for this particular input index
        for ($i = 0; $i < $input_size; $i++) {
            if (!isset($this->m['weights'][$i])) {
                $this->m['weights'][$i] = array_fill(0, $output_size, 0.0);
                $this->v['weights'][$i] = array_fill(0, $output_size, 0.0);
            }
        }
        for ($j = 0; $j < $output_size; $j++) {
            if (!isset($this->m['biases'][$j])) {
                 $this->m['biases'][$j] = 0.0;
                 $this->v['biases'][$j] = 0.0;
            }
        }

        // Update weights
        for ($i = 0; $i < $input_size; $i++) {
            for ($j = 0; $j < $output_size; $j++) {
                $this->m['weights'][$i][$j] = $this->beta1 * $this->m['weights'][$i][$j] + (1 - $this->beta1) * $dWeights[$i][$j];
                $this->v['weights'][$i][$j] = $this->beta2 * $this->v['weights'][$i][$j] + (1 - $this->beta2) * ($dWeights[$i][$j] ** 2);

                $mHat = $this->m['weights'][$i][$j] / (1 - $this->beta1 ** $this->t);
                $vHat = $this->v['weights'][$i][$j] / (1 - $this->beta2 ** $this->t);

                $weights[$i][$j] -= $this->lr * $mHat / (sqrt($vHat) + $this->epsilon);
            }
        }

        // Update biases
        for ($j = 0; $j < $output_size; $j++) {
            $this->m['biases'][$j] = $this->beta1 * $this->m['biases'][$j] + (1 - $this->beta1) * $dBiases[$j];
            $this->v['biases'][$j] = $this->beta2 * $this->v['biases'][$j] + (1 - $this->beta2) * ($dBiases[$j] ** 2);

            $mHat = $this->m['biases'][$j] / (1 - $this->beta1 ** $this->t);
            $vHat = $this->v['biases'][$j] / (1 - $this->beta2 ** $this->t);

            $biases[$j] -= $this->lr * $mHat / (sqrt($vHat) + $this->epsilon);
        }
    }

    public function getLearningRate(): float { return $this->lr; }
}
