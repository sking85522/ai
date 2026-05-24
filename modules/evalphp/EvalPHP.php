<?php
namespace EvalPHP;

class EvalPHP {
    // Main entry point
}

class Metrics {
    public static function accuracy(array $yTrue, array $yPred): float {
        $correct = 0;
        $total = count($yTrue);
        for ($i = 0; $i < $total; $i++) {
            if ($yTrue[$i] === $yPred[$i]) {
                $correct++;
            }
        }
        return $total > 0 ? $correct / $total : 0.0;
    }

    public static function perplexity(float $loss): float {
        return exp($loss);
    }

    public static function bleuScore(array $reference, array $candidate): float {
        // Mock simple unigram precision for BLEU
        $refCounts = array_count_values($reference);
        $candCounts = array_count_values($candidate);

        $matches = 0;
        foreach ($candCounts as $word => $count) {
            if (isset($refCounts[$word])) {
                $matches += min($count, $refCounts[$word]);
            }
        }

        $total = count($candidate);
        return $total > 0 ? $matches / $total : 0.0;
    }
}
