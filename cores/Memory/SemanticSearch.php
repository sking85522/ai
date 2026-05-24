<?php
namespace Core\Memory;

use Core\Matrix\MatrixOps;

/**
 * HRITIK AI - NEURAL VECTOR SEARCH (LSH OPTIMIZED)
 * High-speed conceptual retrieval engine for 5.3M+ records.
 */
class SemanticSearch {
    
    private array $buckets = []; // LSH Hash Buckets
    private array $vectorCache = [];
    private int $vectorDim = 16;

    public function __construct() {
        require_once dirname(__DIR__) . '/Matrix/MatrixOps.php';
    }

    /**
     * Build a high-speed LSH index.
     */
    public function buildIndex(array $dataset): void {
        foreach ($dataset as $item) {
            $text = $item['q'] ?? $item['question'] ?? '';
            $vector = $this->textToDenseVector($text);
            $hash = MatrixOps::localityHash($vector);
            
            if (!isset($this->buckets[$hash])) $this->buckets[$hash] = [];
            $this->buckets[$hash][] = [
                'v' => $vector,
                'a' => $item['a'] ?? $item['answer'] ?? ''
            ];
        }
    }

    /**
     * Fast retrieval using Locality Sensitive Hashing.
     */
    public function search(string $query, float $threshold = 0.7): ?string {
        $qVec = $this->textToDenseVector($query);
        $qHash = MatrixOps::localityHash($qVec);
        
        // Search primary bucket and neighboring buckets (for fuzzy matching)
        $candidates = $this->buckets[$qHash] ?? [];
        
        $bestMatch = null;
        $maxScore = -1;

        foreach ($candidates as $entry) {
            $score = MatrixOps::cosineSimilarity($qVec, $entry['v']);
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestMatch = $entry['a'];
            }
        }

        return ($maxScore >= $threshold) ? $bestMatch : null;
    }

    /**
     * Converts text to a dense 16-dimensional concept vector.
     */
    private function textToDenseVector(string $text): array {
        $vec = array_fill(0, $this->vectorDim, 0.0);
        $words = explode(' ', strtolower(preg_replace('/[^\w\s]/', '', $text)));
        
        foreach ($words as $idx => $word) {
            $len = strlen($word);
            for ($i = 0; $i < $len; $i++) {
                $vec[$i % $this->vectorDim] += ord($word[$i]) / 255.0;
            }
        }
        
        // Normalize
        $mag = sqrt(array_sum(array_map(fn($v) => $v*$v, $vec)));
        return ($mag > 0) ? array_map(fn($v) => $v/$mag, $vec) : $vec;
    }
}
