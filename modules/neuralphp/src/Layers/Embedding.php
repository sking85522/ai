<?php

namespace NeuralPHP\Layers;

class Embedding
{
    private $vocab_size;
    private $embedding_dim;
    private $weights = [];
    private $inputs = []; // Store inputs for backprop

    public function __construct(int $vocab_size, int $embedding_dim)
    {
        $this->vocab_size = $vocab_size;
        $this->embedding_dim = $embedding_dim;

        // Initialize embeddings with random small values
        $limit = sqrt(6 / ($vocab_size + $embedding_dim));
        for ($i = 0; $i < $vocab_size; $i++) {
            $row = [];
            for ($j = 0; $j < $embedding_dim; $j++) {
                $row[] = ($this->randFloat() * 2 * $limit) - $limit;
            }
            $this->weights[] = $row;
        }
    }

    private function randFloat()
    {
        return mt_rand() / mt_getrandmax();
    }

    /**
     * Forward pass
     * @param array $inputs Array of integer token IDs (sequence)
     * @return array 2D array of embedded vectors (sequence length x embedding dim)
     */
    public function forward(array $inputs): array
    {
        $this->inputs = $inputs;
        $outputs = [];
        foreach ($inputs as $token_id) {
            // Handle unknown tokens out of bounds (should ideally be mapped to UNK)
            if ($token_id >= $this->vocab_size || $token_id < 0) {
                $token_id = 0; // Default to UNK which is 0
            }
            $outputs[] = $this->weights[$token_id];
        }
        return $outputs;
    }

    /**
     * Backward pass
     * @param array $dOutputs Derivative of loss w.r.t. outputs (2D array: seq_len x embed_dim)
     * @param object $optimizer
     * @return array Empty array (Embedding is usually the first layer, so we don't pass gradients further down)
     */
    public function backward(array $dOutputs, $optimizer): array
    {
        // $dOutputs has shape [seq_len, embed_dim]
        // We need to accumulate gradients for the weights that were used

        $dWeights = array_fill(0, $this->vocab_size, array_fill(0, $this->embedding_dim, 0.0));

        foreach ($this->inputs as $seq_idx => $token_id) {
            if ($token_id >= $this->vocab_size || $token_id < 0) {
                $token_id = 0;
            }
            for ($j = 0; $j < $this->embedding_dim; $j++) {
                $dWeights[$token_id][$j] += $dOutputs[$seq_idx][$j];
            }
        }

        $dBiases = array_fill(0, $this->embedding_dim, 0.0); // No biases in embedding, but optimizer needs it to infer $output_size
        $emptyBiases = $dBiases;

        // Optimizer update expects flat or matching dimension biases, we pass dummies
        // Since Optimizer expects weights format, it updates $this->weights
        $optimizer->update($this->weights, $emptyBiases, $dWeights, $dBiases);

        return [];
    }
}
