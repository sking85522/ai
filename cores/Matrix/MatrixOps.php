<?php
namespace Core\Matrix;

/**
 * HRITIK AI - ADVANCED MATRIX ENGINE (PRO)
 * High-performance neural math for semantic search and deep learning.
 */
$numPhpDir = dirname(__DIR__, 2) . '/modules/numphp';
if (file_exists($numPhpDir . '/autoload.php')) {
    require_once $numPhpDir . '/autoload.php';
}
if (file_exists($numPhpDir . '/NumPHP.php')) {
    require_once $numPhpDir . '/NumPHP.php';
}

use NumPHP\NumPHP;
use NumPHP\Core\NDArray;

class MatrixOps {
    
    /**
     * UNIVERSAL PROXY: Magic access to specialized libraries.
     */
    public static function __callStatic($name, $arguments) {
        if (method_exists('NumPHP\NumPHP', $name)) {
            return forward_static_call_array(['NumPHP\NumPHP', $name], $arguments);
        }
        return null;
    }

    /**
     * Optimized Cosine Similarity for Neural Retrieval.
     */
    public static function cosineSimilarity(array $v1, array $v2): float {
        $dot = 0.0; $mag1 = 0.0; $mag2 = 0.0;
        $count = count($v1);
        
        for ($i = 0; $i < $count; $i++) {
            $a = $v1[$i]; $b = $v2[$i] ?? 0;
            $dot += $a * $b;
            $mag1 += $a * $a;
            $mag2 += $b * $b;
        }
        
        return ($mag1 && $mag2) ? ($dot / (sqrt($mag1) * sqrt($mag2))) : 0.0;
    }

    /**
     * Generates a "Neural Hash" for Locality Sensitive Hashing (LSH).
     * Helps find similar concepts without scanning the entire 5.3M dataset.
     */
    public static function localityHash(array $vector, int $planes = 8): string {
        $hash = '';
        for ($i = 0; $i < $planes; $i++) {
            $dot = 0;
            // Simulated random plane projection
            foreach ($vector as $val) $dot += $val * sin($i + 1);
            $hash .= ($dot >= 0) ? '1' : '0';
        }
        return $hash;
    }

    /**
     * Fast Softmax for Neural Confidence.
     */
    public static function softmax(array $values): array {
        $max = max($values);
        $exp = array_map(fn($v) => exp($v - $max), $values);
        $sum = array_sum($exp);
        return array_map(fn($v) => $v / ($sum ?: 1), $exp);
    }
    
    public static function create(array $data) { return NumPHP::array($data); }

    public static function inverse($matrix) {
        return NumPHP::inv($matrix);
    }
}
