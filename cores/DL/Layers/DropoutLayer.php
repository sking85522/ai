<?php
namespace Core\DL\Layers;

/**
 * HRITIK AI - NEURAL DROPOUT LAYER
 * Randomly deactivates neurons during training to prevent overfitting.
 */
class DropoutLayer {
    
    private float $rate;
    private array $mask = [];

    public function __construct(float $rate = 0.5) {
        $this->rate = $rate;
    }

    /**
     * Forward pass with dropout mask.
     */
    public function forward(array $inputs, bool $isTraining = true): array {
        if (!$isTraining) return $inputs;

        $outputs = [];
        $this->mask = [];
        foreach ($inputs as $i => $val) {
            $keep = (rand(0, 100) / 100) > $this->rate;
            $this->mask[$i] = $keep ? 1.0 : 0.0;
            $outputs[$i] = $val * ($this->mask[$i] / (1 - $this->rate));
        }
        return $outputs;
    }
}
