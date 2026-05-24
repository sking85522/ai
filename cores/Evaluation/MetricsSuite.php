<?php
namespace Core\Evaluation;
use Core\Evaluation\ConfusionMatrix\ConfusionMatrix;

class MetricsSuite {
    /**
     * Returns a full metrics report for a model.
     */
    public function getFullReport(array $yTrue, array $yPred): array {
        $cm = new \Core\Evaluation\ConfusionMatrix\ConfusionMatrix();
        
        return [
            'accuracy' => Metrics::accuracy($yTrue, $yPred),
            'precision' => Metrics::precision($yTrue, $yPred),
            'recall' => Metrics::recall($yTrue, $yPred),
            'f1_score' => Metrics::f1Score($yTrue, $yPred),
            'confusion_matrix' => $cm->generate($yTrue, $yPred),
            'timestamp' => time()
        ];
    }

    public function evaluateClassification(array $truth, array $pred): array
    {
        $n = min(count($truth), count($pred));
        if ($n <= 0) {
            return ['accuracy' => 0.0, 'precision' => 0.0, 'recall' => 0.0, 'f1' => 0.0];
        }

        $correct = 0;
        $labels = array_values(array_unique(array_merge($truth, $pred)));
        $tp = $fp = $fn = array_fill_keys($labels, 0);

        for ($i = 0; $i < $n; $i++) {
            $t = (string) $truth[$i];
            $p = (string) $pred[$i];
            if ($t === $p) {
                $correct++;
                $tp[$t] = ($tp[$t] ?? 0) + 1;
            } else {
                $fp[$p] = ($fp[$p] ?? 0) + 1;
                $fn[$t] = ($fn[$t] ?? 0) + 1;
            }
        }

        $precision = $recall = $f1 = 0.0;
        $k = max(1, count($labels));
        foreach ($labels as $label) {
            $p = ($tp[$label] ?? 0) / max(1, (($tp[$label] ?? 0) + ($fp[$label] ?? 0)));
            $r = ($tp[$label] ?? 0) / max(1, (($tp[$label] ?? 0) + ($fn[$label] ?? 0)));
            $f = ($p + $r) > 0 ? (2 * $p * $r / ($p + $r)) : 0.0;
            $precision += $p;
            $recall += $r;
            $f1 += $f;
        }

        return [
            'accuracy' => round($correct / $n, 4),
            'precision' => round($precision / $k, 4),
            'recall' => round($recall / $k, 4),
            'f1' => round($f1 / $k, 4),
        ];
    }
}
