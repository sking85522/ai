<?php
namespace Core\GenerativeAI\Attention;

/**
 * HRITIK AI - MULTI-HEAD ATTENTION CORE
 * Allows the AI to focus on multiple aspects of context simultaneously.
 */
class MultiHeadAttention {
    
    private int $heads = 4;
    private int $dim = 32;

    public function __construct(int $heads = 4, int $dim = 32) {
        $this->heads = $heads;
        $this->dim = $dim;
    }

    /**
     * Splits vectors into multiple heads and calculates combined attention.
     */
    public function compute(array $embeddings): array {
        $n = count($embeddings);
        $headDim = (int)($this->dim / $this->heads);
        $combinedAttention = array_fill(0, $n, array_fill(0, $n, 0.0));

        for ($h = 0; $h < $this->heads; $h++) {
            $headScore = $this->calculateSingleHead($embeddings, $h, $headDim);
            // Merge head scores
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $combinedAttention[$i][$j] += $headScore[$i][$j] / $this->heads;
                }
            }
        }

        return $combinedAttention;
    }

    private function calculateSingleHead(array $embeddings, int $headIdx, int $headDim): array {
        $n = count($embeddings);
        $scores = array_fill(0, $n, array_fill(0, $n, 0.0));
        
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                // Dot product on a subset of the embedding dimensions (the "head")
                $dot = 0;
                $start = $headIdx * $headDim;
                for ($k = $start; $k < $start + $headDim; $k++) {
                    $dot += ($embeddings[$i][$k] ?? 0) * ($embeddings[$j][$k] ?? 0);
                }
                $scores[$i][$j] = $dot / sqrt($headDim);
            }
        }
        
        return $scores;
    }
}
