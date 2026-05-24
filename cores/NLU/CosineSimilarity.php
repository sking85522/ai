<?php
namespace Core\NLU;

/**
 * Cosine Similarity Calculator
 * Measures semantic closeness between two TF-IDF vectors.
 * Score ranges from 0.0 (unrelated) to 1.0 (identical).
 */
class CosineSimilarity {

    /**
     * Calculate cosine similarity between two sparse vectors
     * @param array $vecA Associative array [term => weight]
     * @param array $vecB Associative array [term => weight]
     * @return float 0.0 to 1.0
     */
    public static function calculate(array $vecA, array $vecB): float {
        if (empty($vecA) || empty($vecB)) return 0.0;

        // Dot product (only for shared terms)
        $dotProduct = 0.0;
        foreach ($vecA as $term => $weightA) {
            if (isset($vecB[$term])) {
                $dotProduct += $weightA * $vecB[$term];
            }
        }

        if ($dotProduct === 0.0) return 0.0;

        // Magnitudes
        $magA = self::magnitude($vecA);
        $magB = self::magnitude($vecB);

        if ($magA === 0.0 || $magB === 0.0) return 0.0;

        return $dotProduct / ($magA * $magB);
    }

    /**
     * Find the top-N most similar documents from a set
     * @param array $queryVec Query TF-IDF vector
     * @param array $docVectors Array of [id => vector]
     * @param int $topN Number of results
     * @param float $threshold Minimum similarity score
     * @return array Sorted by similarity desc
     */
    public static function findSimilar(array $queryVec, array $docVectors, int $topN = 5, float $threshold = 0.05): array {
        $scores = [];

        foreach ($docVectors as $id => $docVec) {
            $sim = self::calculate($queryVec, $docVec);
            if ($sim >= $threshold) {
                $scores[] = ['id' => $id, 'score' => $sim];
            }
        }

        // Sort by score descending
        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scores, 0, $topN);
    }

    /**
     * Calculate vector magnitude (L2 norm)
     */
    private static function magnitude(array $vec): float {
        $sum = 0.0;
        foreach ($vec as $weight) {
            $sum += $weight * $weight;
        }
        return sqrt($sum);
    }
}
