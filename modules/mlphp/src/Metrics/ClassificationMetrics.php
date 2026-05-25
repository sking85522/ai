<?php

namespace MLPHP\Metrics;

/**
 * Extended classification metrics — F1, Precision, Recall, Confusion Matrix, ROC-AUC.
 */
class ClassificationMetrics
{
    public static function accuracy_score(array $y_true, array $y_pred): float
    {
        $correct = 0;
        for ($i = 0; $i < count($y_true); $i++) {
            if ($y_true[$i] == $y_pred[$i]) $correct++;
        }
        return $correct / count($y_true);
    }

    public static function precision_score(array $y_true, array $y_pred, $positive = 1): float
    {
        $tp = 0; $fp = 0;
        for ($i = 0; $i < count($y_true); $i++) {
            if ($y_pred[$i] == $positive) {
                if ($y_true[$i] == $positive) $tp++;
                else $fp++;
            }
        }
        return ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0.0;
    }

    public static function recall_score(array $y_true, array $y_pred, $positive = 1): float
    {
        $tp = 0; $fn = 0;
        for ($i = 0; $i < count($y_true); $i++) {
            if ($y_true[$i] == $positive) {
                if ($y_pred[$i] == $positive) $tp++;
                else $fn++;
            }
        }
        return ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0.0;
    }

    public static function f1_score(array $y_true, array $y_pred, $positive = 1): float
    {
        $p = self::precision_score($y_true, $y_pred, $positive);
        $r = self::recall_score($y_true, $y_pred, $positive);
        return ($p + $r) > 0 ? 2 * ($p * $r) / ($p + $r) : 0.0;
    }

    public static function confusion_matrix(array $y_true, array $y_pred): array
    {
        $labels = array_values(array_unique(array_merge($y_true, $y_pred)));
        sort($labels);
        $n = count($labels);
        $labelMap = array_flip($labels);

        $matrix = array_fill(0, $n, array_fill(0, $n, 0));
        for ($i = 0; $i < count($y_true); $i++) {
            $trueIdx = $labelMap[$y_true[$i]];
            $predIdx = $labelMap[$y_pred[$i]];
            $matrix[$trueIdx][$predIdx]++;
        }

        return ['matrix' => $matrix, 'labels' => $labels];
    }

    public static function classification_report(array $y_true, array $y_pred): array
    {
        $labels = array_values(array_unique(array_merge($y_true, $y_pred)));
        sort($labels);

        $report = [];
        foreach ($labels as $label) {
            $report[$label] = [
                'precision' => self::precision_score($y_true, $y_pred, $label),
                'recall' => self::recall_score($y_true, $y_pred, $label),
                'f1-score' => self::f1_score($y_true, $y_pred, $label),
                'support' => count(array_filter($y_true, fn($v) => $v == $label)),
            ];
        }
        $report['accuracy'] = self::accuracy_score($y_true, $y_pred);
        return $report;
    }
}
