<?php
namespace Core\GenerativeAI;

/**
 * HRITIK AI - NEURAL ATTENTION TRANSFORMER (PRO)
 * Uses high-dimensional matrix math and positional encodings to improve text generation.
 */
class Transformers {
    
    private int $dim = 32; // Vector dimension for deep conceptual mapping

    public function __construct() {
        require_once dirname(__DIR__) . '/Matrix/MatrixOps.php';
    }

    /**
     * Calculates Self-Attention using Scaled Dot-Product mechanism.
     */
    public function calculateAttention(string $sentence): array {
        $tokens = explode(' ', strtolower(trim($sentence)));
        $n = count($tokens);
        if ($n === 0) return [];

        // 1. Generate Embeddings + Positional Encodings
        $embeddings = [];
        foreach ($tokens as $idx => $token) {
            $embeddings[] = $this->getPositionalEmbedding($token, $idx);
        }

        // 2. Multi-Head Attention Simulation (QK^T / sqrt(dk))
        $attentionMatrix = [];
        $sqrtD = sqrt($this->dim);

        for ($i = 0; $i < $n; $i++) {
            $scores = [];
            for ($j = 0; $j < $n; $j++) {
                $score = $this->dotProduct($embeddings[$i], $embeddings[$j]);
                $scores[$j] = $score / $sqrtD;
            }
            $attentionMatrix[$i] = $this->softmax($scores);
        }

        return [
            'tokens' => $tokens,
            'matrix' => $attentionMatrix,
            'top_context_token' => $tokens[array_search(max($attentionMatrix[$n-1] ?? [0]), $attentionMatrix[$n-1] ?? [0])] ?? ''
        ];
    }

    /**
     * Creates a vector that represents both the word's meaning and its position.
     */
    private function getPositionalEmbedding(string $word, int $pos): array {
        $vec = array_fill(0, $this->dim, 0.0);
        $len = strlen($word);

        // Meaning Part (Char frequency & structure)
        for ($i = 0; $i < $len; $i++) {
            $vec[$i % $this->dim] += ord($word[$i]) / 255.0;
        }

        // Positional Part (Sin/Cos encoding simulation)
        for ($i = 0; $i < $this->dim; $i++) {
            if ($i % 2 == 0) {
                $vec[$i] += sin($pos / pow(10000, (2 * $i) / $this->dim));
            } else {
                $vec[$i] += cos($pos / pow(10000, (2 * $i) / $this->dim));
            }
        }

        return $vec;
    }

    private function dotProduct(array $v1, array $v2): float {
        $dot = 0;
        foreach ($v1 as $i => $val) $dot += $val * ($v2[$i] ?? 0);
        return $dot;
    }

    private function softmax(array $scores): array {
        $max = max($scores); // For numerical stability
        $exp = array_map(fn($v) => exp($v - $max), $scores);
        $sum = array_sum($exp);
        return array_map(fn($v) => $v / ($sum ?: 1), $exp);
    }
}
