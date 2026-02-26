<?php
class Predictor
{
    public function predict(array $tokens, Supervised $supervised, Unsupervised $unsupervised): array
    {
        $supervisedPrediction = $supervised->predict($tokens);
        $keywords = $unsupervised->extractKeywords($tokens);

        $vector = $this->vectorize($tokens);
        $reference = array_fill(0, count($vector), 0.5);
        $similarity = $this->cosineSimilarity($vector, $reference);

        return [
            'intent' => $supervisedPrediction['label'],
            'confidence' => round(($supervisedPrediction['score'] * 0.7) + ($similarity * 0.3), 4),
            'keywords' => $keywords,
            'vector' => $vector,
        ];
    }

    public function vectorize(array $tokens, int $dims = 8): array
    {
        $vector = array_fill(0, $dims, 0.0);
        foreach ($tokens as $token) {
            $hash = crc32($token);
            $vector[$hash % $dims] += 1.0;
        }
        $norm = sqrt(array_sum(array_map(static fn($v) => $v * $v, $vector)));
        if ($norm > 0.0) {
            $vector = array_map(static fn($v) => round($v / $norm, 4), $vector);
        }
        return $vector;
    }

    public function cosineSimilarity(array $a, array $b): float
    {
        $len = min(count($a), count($b));
        $dot = 0.0;
        $aNorm = 0.0;
        $bNorm = 0.0;

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $aNorm += $a[$i] * $a[$i];
            $bNorm += $b[$i] * $b[$i];
        }

        if ($aNorm <= 0.0 || $bNorm <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($aNorm) * sqrt($bNorm));
    }
}
