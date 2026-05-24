<?php
namespace Core\Matrix;

/**
 * HRITIK AI - VECTOR MATH
 * Specialized in high-speed vector calculations for Semantic Search and Similarity.
 */
class VectorMath {

    /**
     * Calculate Cosine Similarity between two vectors.
     */
    public static function cosineSimilarity(array $vec1, array $vec2): float {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vec1 as $i => $val) {
            $dotProduct += $val * ($vec2[$i] ?? 0);
            $normA += $val * $val;
        }

        foreach ($vec2 as $val) {
            $normB += $val * $val;
        }

        if ($normA == 0 || $normB == 0) return 0;
        
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Euclidean Distance (L2 Norm)
     */
    public static function euclideanDistance(array $vec1, array $vec2): float {
        $sum = 0;
        foreach ($vec1 as $i => $val) {
            $diff = $val - ($vec2[$i] ?? 0);
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }
}
