<?php
namespace NeuralPHP\Layers;

/**
 * Dropout layer — Randomly zeros elements during training to prevent overfitting.
 */
class Dropout {
    private $rate;
    private $mask = [];
    private $training = true;

    public function __construct(float $rate = 0.5) {
        $this->rate = $rate;
    }

    public function setTraining(bool $training): void {
        $this->training = $training;
    }

    public function forward(array $input): array {
        if (!$this->training) {
            return $input;
        }
        $this->mask = [];
        $output = [];
        $scale = 1.0 / (1.0 - $this->rate);
        for ($i = 0; $i < count($input); $i++) {
            if (mt_rand(0, 1000) / 1000.0 > $this->rate) {
                $this->mask[$i] = 1;
                $output[$i] = $input[$i] * $scale;
            } else {
                $this->mask[$i] = 0;
                $output[$i] = 0.0;
            }
        }
        return $output;
    }

    public function backward(array $gradOutput): array {
        $grad = [];
        $scale = 1.0 / (1.0 - $this->rate);
        for ($i = 0; $i < count($gradOutput); $i++) {
            $grad[$i] = $gradOutput[$i] * $this->mask[$i] * $scale;
        }
        return $grad;
    }
}
