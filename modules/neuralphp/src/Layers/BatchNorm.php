<?php
namespace NeuralPHP\Layers;

/**
 * Batch Normalization — Normalizes layer inputs for faster, more stable training.
 */
class BatchNorm {
    private $gamma;
    private $beta;
    private $epsilon;
    private $runningMean = null;
    private $runningVar = null;
    private $momentum;
    private $training = true;

    public function __construct(int $size, float $epsilon = 1e-5, float $momentum = 0.1) {
        $this->gamma = array_fill(0, $size, 1.0);
        $this->beta = array_fill(0, $size, 0.0);
        $this->epsilon = $epsilon;
        $this->momentum = $momentum;
    }

    public function setTraining(bool $training): void {
        $this->training = $training;
    }

    public function forward(array $input): array {
        $size = count($input);

        if ($this->training) {
            $mean = array_sum($input) / $size;
            $var = 0.0;
            foreach ($input as $v) { $var += ($v - $mean) ** 2; }
            $var /= $size;

            // Update running stats
            if ($this->runningMean === null) {
                $this->runningMean = $mean;
                $this->runningVar = $var;
            } else {
                $this->runningMean = (1 - $this->momentum) * $this->runningMean + $this->momentum * $mean;
                $this->runningVar = (1 - $this->momentum) * $this->runningVar + $this->momentum * $var;
            }
        } else {
            $mean = $this->runningMean ?? 0.0;
            $var = $this->runningVar ?? 1.0;
        }

        $output = [];
        for ($i = 0; $i < $size; $i++) {
            $normalized = ($input[$i] - $mean) / sqrt($var + $this->epsilon);
            $output[$i] = $this->gamma[$i % count($this->gamma)] * $normalized + $this->beta[$i % count($this->beta)];
        }
        return $output;
    }
}
