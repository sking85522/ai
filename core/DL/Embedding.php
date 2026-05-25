<?php
namespace Core\DL;

require_once __DIR__ . '/Tensor.php';

/**
 * HRITIK AI - EMBEDDING LAYER
 * Maps discrete word indices to dense continuous vectors.
 *
 * This is a trainable lookup table of shape (vocabSize × embeddingDim).
 * Forward: token_id → embedding vector (row lookup)
 * Backward: accumulate gradients for accessed rows only.
 *
 * Parameters: vocabSize × embeddingDim
 */
class Embedding {

    /** @var Tensor Weight matrix (vocabSize × embeddingDim) */
    public Tensor $weights;

    /** @var Tensor Gradient accumulator (same shape as weights) */
    public Tensor $gradients;

    private int $vocabSize;
    private int $embeddingDim;

    public function __construct(int $vocabSize, int $embeddingDim) {
        $this->vocabSize = $vocabSize;
        $this->embeddingDim = $embeddingDim;

        // Initialize with small random values (Xavier-like)
        $this->weights = Tensor::xavierInit($vocabSize, $embeddingDim);
        $this->gradients = Tensor::zeros($vocabSize, $embeddingDim);
    }

    /**
     * Forward pass: look up embedding vector for a token.
     *
     * @param int $tokenId Word index (0 to vocabSize-1)
     * @return float[] Embedding vector of length embeddingDim
     */
    public function forward(int $tokenId): array {
        if ($tokenId < 0 || $tokenId >= $this->vocabSize) {
            return array_fill(0, $this->embeddingDim, 0.0);
        }
        return $this->weights->getRow($tokenId);
    }

    /**
     * Backward pass: accumulate gradient for this token's embedding.
     *
     * @param float[] $grad    Gradient vector (length embeddingDim)
     * @param int     $tokenId Token whose embedding to update
     */
    public function backward(array $grad, int $tokenId): void {
        if ($tokenId < 0 || $tokenId >= $this->vocabSize) return;
        $this->weights->addToRow($tokenId, $grad);
    }

    /**
     * Reset gradients to zero before each training step.
     */
    public function zeroGrad(): void {
        $this->gradients->zero();
    }

    /**
     * Get total parameter count.
     */
    public function paramCount(): int {
        return $this->vocabSize * $this->embeddingDim;
    }

    /**
     * Export weights for serialization.
     */
    public function exportWeights(): array {
        return [
            'type' => 'embedding',
            'vocab_size' => $this->vocabSize,
            'embedding_dim' => $this->embeddingDim,
            'data' => $this->weights->data,
        ];
    }

    /**
     * Import weights from serialized data.
     */
    public function importWeights(array $saved): void {
        if (isset($saved['data'])) {
            $this->weights->data = $saved['data'];
        }
    }
}
