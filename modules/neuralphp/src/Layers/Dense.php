<?php

namespace NeuralPHP\Layers;

use NeuralPHP\Activations\Activation;
use NeuralPHP\NeuralPHP;

class Dense
{
    private $input_size;
    private $output_size;
    /** @var Activation|null */
    private $activation;

    private $weights = [];
    private $biases = [];

    // Cache for backprop
    private $inputs = [];
    private $z = [];

    public function __construct(int $input_size, int $output_size, ?string $activation = null)
    {
        $this->input_size = $input_size;
        $this->output_size = $output_size;

        if ($activation) {
            $this->activation = NeuralPHP::getActivation($activation);
        }

        // Initialize weights (Xavier initialization)
        $limit = sqrt(6 / ($input_size + $output_size));
        for ($i = 0; $i < $input_size; $i++) {
            $row = [];
            for ($j = 0; $j < $output_size; $j++) {
                // Random float between -$limit and $limit
                $row[] = ($this->randFloat() * 2 * $limit) - $limit;
            }
            $this->weights[] = $row;
        }

        // Initialize biases
        for ($j = 0; $j < $output_size; $j++) {
            $this->biases[] = 0.0;
        }
    }

    private function randFloat()
    {
        return mt_rand() / mt_getrandmax();
    }

    /**
     * Forward pass
     */
    public function forward(array $inputs): array
    {
        $this->inputs = $inputs;
        $this->z = [];

        // Matrix multiplication + biases
        for ($j = 0; $j < $this->output_size; $j++) {
            $sum = $this->biases[$j];
            for ($i = 0; $i < $this->input_size; $i++) {
                $sum += $inputs[$i] * $this->weights[$i][$j];
            }
            $this->z[] = $sum;
        }

        if ($this->activation) {
            return $this->activation->forward($this->z);
        }
        return $this->z;
    }

    /**
     * Backward pass
     * @param array $dOutputs Derivative of loss w.r.t outputs of this layer
     * @param object $optimizer SGD Optimizer instance
     * @return array Derivative of loss w.r.t inputs to this layer (to pass to previous layer)
     */
    public function backward(array $dOutputs, $optimizer): array
    {
        $dZ = [];

        // Apply derivative of activation function
        if ($this->activation) {
            $dAct = $this->activation->derivative($this->z);
            for ($i = 0; $i < $this->output_size; $i++) {
                $dZ[] = $dOutputs[$i] * $dAct[$i];
            }
        } else {
            $dZ = $dOutputs;
        }

        // Gradients for weights and biases
        $dWeights = [];
        for ($i = 0; $i < $this->input_size; $i++) {
            $row = [];
            for ($j = 0; $j < $this->output_size; $j++) {
                $row[] = $this->inputs[$i] * $dZ[$j];
            }
            $dWeights[] = $row;
        }

        $dBiases = $dZ;

        // Gradient for inputs (to pass down the chain)
        $dInputs = [];
        for ($i = 0; $i < $this->input_size; $i++) {
            $sum = 0;
            for ($j = 0; $j < $this->output_size; $j++) {
                $sum += $dZ[$j] * $this->weights[$i][$j];
            }
            $dInputs[] = $sum;
        }

        // Update weights and biases via Optimizer
        $optimizer->update($this->weights, $this->biases, $dWeights, $dBiases);

        return $dInputs;
    }
}
