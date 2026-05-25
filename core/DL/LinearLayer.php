<?php
namespace Core\DL;

require_once __DIR__ . '/Tensor.php';

/**
 * HRITIK AI - LINEAR LAYER (Fully Connected / Dense)
 * Computes: output = W @ input + bias
 *
 * Forward: linear transformation
 * Backward: computes gradients for weights, bias, and input
 *
 * Parameters: outputSize × inputSize (weights) + outputSize (bias)
 */
class LinearLayer {

    /** @var Tensor Weight matrix (outputSize × inputSize) */
    public Tensor $weights;

    /** @var float[] Bias vector (outputSize) */
    public array $bias;

    /** @var Tensor Gradient accumulator for weights */
    public Tensor $dWeights;

    /** @var float[] Gradient accumulator for bias */
    public array $dBias;

    private int $inputSize;
    private int $outputSize;

    /** @var array Cached inputs for backward pass, indexed by timestep */
    private array $cachedInputs = [];

    public function __construct(int $inputSize, int $outputSize) {
        $this->inputSize = $inputSize;
        $this->outputSize = $outputSize;

        // Xavier initialization
        $this->weights = Tensor::xavierInit($outputSize, $inputSize);
        $this->bias = array_fill(0, $outputSize, 0.0);

        // Gradient accumulators
        $this->dWeights = Tensor::zeros($outputSize, $inputSize);
        $this->dBias = array_fill(0, $outputSize, 0.0);
    }

    /**
     * Forward pass: output = W @ input + bias
     *
     * @param float[] $input Input vector (length inputSize)
     * @param int     $t     Timestep index (for caching during sequences)
     * @return float[] Output vector (length outputSize)
     */
    public function forward(array $input, int $t = 0): array {
        // Cache input for backward pass
        $this->cachedInputs[$t] = $input;

        // W @ input
        $output = $this->weights->matvec($input);

        // + bias
        $outSize = $this->outputSize;
        for ($i = 0; $i < $outSize; $i++) {
            $output[$i] += $this->bias[$i];
        }

        return $output;
    }

    /**
     * Backward pass: computes gradients and returns input gradient.
     *
     * @param float[] $dOutput Gradient of loss w.r.t. output (length outputSize)
     * @param int     $t       Timestep index
     * @return float[] Gradient of loss w.r.t. input (length inputSize)
     */
    public function backward(array $dOutput, int $t = 0): array {
        $cachedInput = $this->cachedInputs[$t] ?? array_fill(0, $this->inputSize, 0.0);

        // dW += outer(dOutput, input)
        $this->dWeights->addOuterProduct($dOutput, $cachedInput);

        // dBias += dOutput
        foreach ($dOutput as $i => $val) {
            $this->dBias[$i] += $val;
        }

        // dInput = W^T @ dOutput
        return $this->weights->transposeMulVec($dOutput);
    }

    /**
     * Reset all gradients to zero.
     */
    public function zeroGrad(): void {
        $this->dWeights->zero();
        $this->dBias = array_fill(0, $this->outputSize, 0.0);
    }

    /**
     * Clear cached data (call after each training step to free memory).
     */
    public function clearCache(): void {
        $this->cachedInputs = [];
    }

    /**
     * Total parameter count.
     */
    public function paramCount(): int {
        return ($this->outputSize * $this->inputSize) + $this->outputSize;
    }

    /**
     * Export weights for serialization.
     */
    public function exportWeights(): array {
        return [
            'type' => 'linear',
            'input_size' => $this->inputSize,
            'output_size' => $this->outputSize,
            'weights' => $this->weights->data,
            'bias' => $this->bias,
        ];
    }

    /**
     * Import weights from serialized data.
     */
    public function importWeights(array $saved): void {
        if (isset($saved['weights'])) {
            $this->weights->data = $saved['weights'];
        }
        if (isset($saved['bias'])) {
            $this->bias = $saved['bias'];
        }
    }
}
