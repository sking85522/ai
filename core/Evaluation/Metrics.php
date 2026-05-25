<?php
namespace Core\Evaluation;

class Metrics {
    
    /**
     * Accuracy Score: (TP + TN) / Total
     */
    public static function accuracy(array $yTrue, array $yPred): float {
        $correct = 0;
        foreach ($yTrue as $i => $val) {
            if ($val === ($yPred[$i] ?? null)) $correct++;
        }
        return count($yTrue) > 0 ? $correct / count($yTrue) : 0.0;
    }

    /**
     * Precision Score: TP / (TP + FP)
     */
    public static function precision(array $yTrue, array $yPred, $posLabel = 1): float {
        $tp = 0; $fp = 0;
        foreach ($yPred as $i => $pred) {
            if ($pred === $posLabel) {
                if (($yTrue[$i] ?? null) === $posLabel) $tp++;
                else $fp++;
            }
        }
        return ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0.0;
    }

    /**
     * Recall Score: TP / (TP + FN)
     */
    public static function recall(array $yTrue, array $yPred, $posLabel = 1): float {
        $tp = 0; $fn = 0;
        foreach ($yTrue as $i => $true) {
            if ($true === $posLabel) {
                if (($yPred[$i] ?? null) === $posLabel) $tp++;
                else $fn++;
            }
        }
        return ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0.0;
    }

    /**
     * F1 Score: 2 * (P * R) / (P + R)
     */
    public static function f1Score(array $yTrue, array $yPred, $posLabel = 1): float {
        $p = self::precision($yTrue, $yPred, $posLabel);
        $r = self::recall($yTrue, $yPred, $posLabel);
        return ($p + $r) > 0 ? 2 * ($p * $r) / ($p + $r) : 0.0;
    }

    /**
     * Root Mean Squared Error (RMSE)
     */
    public static function rmse(array $yTrue, array $yPred): float {
        require_once __DIR__ . '/../Matrix/MatrixOps.php';
        $diffs = [];
        foreach ($yTrue as $i => $val) {
            $diffs[] = $val - ($yPred[$i] ?? 0);
        }
        $squares = \Core\Matrix\MatrixOps::create($diffs);
        // Using NumPHP's power or just mapping
        return sqrt(array_sum(array_map(fn($v) => $v**2, $diffs)) / count($diffs));
    }

    /**
     * Mean Absolute Error (MAE)
     */
    public static function mae(array $yTrue, array $yPred): float {
        $sum = 0;
        foreach ($yTrue as $i => $val) {
            $sum += abs($val - ($yPred[$i] ?? 0));
        }
        return count($yTrue) > 0 ? $sum / count($yTrue) : 0.0;
    }
}
