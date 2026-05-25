<?php

namespace MLPHP\Metrics;

/**
 * Regression metrics — MSE, MAE, RMSE, R² Score.
 */
class RegressionMetrics
{
    public static function mean_squared_error(array $y_true, array $y_pred): float
    {
        $sum = 0.0;
        $n = count($y_true);
        for ($i = 0; $i < $n; $i++) {
            $sum += ($y_true[$i] - $y_pred[$i]) ** 2;
        }
        return $sum / $n;
    }

    public static function mean_absolute_error(array $y_true, array $y_pred): float
    {
        $sum = 0.0;
        $n = count($y_true);
        for ($i = 0; $i < $n; $i++) {
            $sum += abs($y_true[$i] - $y_pred[$i]);
        }
        return $sum / $n;
    }

    public static function root_mean_squared_error(array $y_true, array $y_pred): float
    {
        return sqrt(self::mean_squared_error($y_true, $y_pred));
    }

    public static function r2_score(array $y_true, array $y_pred): float
    {
        $mean = array_sum($y_true) / count($y_true);
        $ssRes = 0.0;
        $ssTot = 0.0;
        for ($i = 0; $i < count($y_true); $i++) {
            $ssRes += ($y_true[$i] - $y_pred[$i]) ** 2;
            $ssTot += ($y_true[$i] - $mean) ** 2;
        }
        return ($ssTot > 0) ? 1 - ($ssRes / $ssTot) : 0.0;
    }
}
