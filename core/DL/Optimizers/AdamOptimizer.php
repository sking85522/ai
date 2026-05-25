<?php
namespace Core\DL\Optimizers;

require_once dirname(__DIR__) . '/Tensor.php';
use Core\DL\Tensor;

/**
 * HRITIK AI - ADAM OPTIMIZER (Adaptive Moment Estimation)
 * State-of-the-art training algorithm for neural weight updates.
 *
 * Supports:
 * - 2D Tensor weights (weight matrices)
 * - 1D array weights (bias vectors)
 * - Gradient clipping (prevents exploding gradients in LSTM/RNN)
 * - Learning rate scheduling (warm-up + decay)
 * - Named parameter tracking (each parameter has independent moments)
 */
class AdamOptimizer {

    private float $learningRate;
    private float $baseLR;
    private float $beta1;
    private float $beta2;
    private float $epsilon;
    private float $clipNorm;
    private int $warmupSteps;

    private int $t = 0; // Global timestep

    /** @var array<string, float[]> First moment estimates per parameter */
    private array $m = [];

    /** @var array<string, float[]> Second moment estimates per parameter */
    private array $v = [];

    /**
     * @param float $learningRate Base learning rate
     * @param float $beta1        Exponential decay for first moment (default 0.9)
     * @param float $beta2        Exponential decay for second moment (default 0.999)
     * @param float $epsilon      Numerical stability constant
     * @param float $clipNorm     Max gradient norm (0 = no clipping)
     * @param int   $warmupSteps  Number of steps for learning rate warm-up
     */
    public function __construct(
        float $learningRate = 0.001,
        float $beta1 = 0.9,
        float $beta2 = 0.999,
        float $epsilon = 1e-8,
        float $clipNorm = 5.0,
        int $warmupSteps = 100
    ) {
        $this->learningRate = $learningRate;
        $this->baseLR = $learningRate;
        $this->beta1 = $beta1;
        $this->beta2 = $beta2;
        $this->epsilon = $epsilon;
        $this->clipNorm = $clipNorm;
        $this->warmupSteps = $warmupSteps;
    }

    /**
     * Increment the global timestep. Call once before updating all parameters.
     */
    public function beginStep(): void {
        $this->t++;

        // Learning rate warm-up
        if ($this->warmupSteps > 0 && $this->t <= $this->warmupSteps) {
            $this->learningRate = $this->baseLR * ($this->t / $this->warmupSteps);
        }
    }

    /**
     * Update a 2D Tensor parameter (weight matrices).
     *
     * @param string $name    Unique parameter name (e.g., "lstm1_W")
     * @param Tensor $weights The weight tensor to update in-place
     * @param Tensor $grads   The gradient tensor (will be clipped if needed)
     */
    public function updateTensor(string $name, Tensor $weights, Tensor $grads): void {
        // Gradient clipping
        if ($this->clipNorm > 0) {
            $norm = $grads->norm();
            if ($norm > $this->clipNorm) {
                $grads->scale($this->clipNorm / $norm);
            }
        }

        $size = count($weights->data);
        $this->ensureState($name, $size);

        $t = $this->t;
        $b1 = $this->beta1;
        $b2 = $this->beta2;
        $lr = $this->learningRate;
        $eps = $this->epsilon;

        // Bias correction factors
        $bc1 = 1.0 - pow($b1, $t);
        $bc2 = 1.0 - pow($b2, $t);

        $mRef = &$this->m[$name];
        $vRef = &$this->v[$name];
        $wData = &$weights->data;
        $gData = $grads->data;

        for ($i = 0; $i < $size; $i++) {
            $g = $gData[$i];

            // Update biased first moment estimate
            $mRef[$i] = $b1 * $mRef[$i] + (1.0 - $b1) * $g;

            // Update biased second moment estimate
            $vRef[$i] = $b2 * $vRef[$i] + (1.0 - $b2) * $g * $g;

            // Bias-corrected estimates
            $mHat = $mRef[$i] / $bc1;
            $vHat = $vRef[$i] / $bc2;

            // Update weight
            $wData[$i] -= $lr * $mHat / (sqrt($vHat) + $eps);
        }
    }

    /**
     * Update a 1D array parameter (bias vectors).
     *
     * @param string  $name    Unique parameter name (e.g., "lstm1_b")
     * @param float[] &$weights The bias array to update in-place
     * @param float[] $grads    The gradient array
     */
    public function updateArray(string $name, array &$weights, array $grads): void {
        $size = count($weights);

        // Gradient clipping for 1D
        if ($this->clipNorm > 0) {
            $norm = 0.0;
            foreach ($grads as $g) $norm += $g * $g;
            $norm = sqrt($norm);
            if ($norm > $this->clipNorm) {
                $scale = $this->clipNorm / $norm;
                $grads = array_map(fn($g) => $g * $scale, $grads);
            }
        }

        $this->ensureState($name, $size);

        $t = $this->t;
        $b1 = $this->beta1;
        $b2 = $this->beta2;
        $lr = $this->learningRate;
        $eps = $this->epsilon;

        $bc1 = 1.0 - pow($b1, $t);
        $bc2 = 1.0 - pow($b2, $t);

        $mRef = &$this->m[$name];
        $vRef = &$this->v[$name];

        for ($i = 0; $i < $size; $i++) {
            $g = $grads[$i];
            $mRef[$i] = $b1 * $mRef[$i] + (1.0 - $b1) * $g;
            $vRef[$i] = $b2 * $vRef[$i] + (1.0 - $b2) * $g * $g;

            $mHat = $mRef[$i] / $bc1;
            $vHat = $vRef[$i] / $bc2;

            $weights[$i] -= $lr * $mHat / (sqrt($vHat) + $eps);
        }
    }

    /**
     * Ensure moment arrays exist for a parameter.
     */
    private function ensureState(string $name, int $size): void {
        if (!isset($this->m[$name])) {
            $this->m[$name] = array_fill(0, $size, 0.0);
            $this->v[$name] = array_fill(0, $size, 0.0);
        }
    }

    /**
     * Get current learning rate (useful for logging).
     */
    public function getCurrentLR(): float {
        return $this->learningRate;
    }

    /**
     * Get current timestep.
     */
    public function getTimestep(): int {
        return $this->t;
    }

    /**
     * Export optimizer state for checkpointing.
     */
    public function exportState(): array {
        return [
            't' => $this->t,
            'm' => $this->m,
            'v' => $this->v,
            'lr' => $this->learningRate,
        ];
    }

    /**
     * Import optimizer state from checkpoint.
     */
    public function importState(array $state): void {
        $this->t = $state['t'] ?? 0;
        $this->m = $state['m'] ?? [];
        $this->v = $state['v'] ?? [];
        if (isset($state['lr'])) {
            $this->learningRate = $state['lr'];
        }
    }

    /**
     * Legacy method for backward compatibility with old code.
     * @deprecated Use updateArray() instead.
     */
    public function update(array &$weights, array $gradients, string $layerId): void {
        $this->beginStep();
        $this->updateArray($layerId, $weights, $gradients);
    }
}
