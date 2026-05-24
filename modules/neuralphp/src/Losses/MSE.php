<?php

namespace NeuralPHP\Losses;

class MSE
{
    public function calculate(array $y_true, array $y_pred): float
    {
        $sum = 0.0;
        $n = count($y_true);
        for ($i = 0; $i < $n; $i++) {
            $sum += pow($y_true[$i] - $y_pred[$i], 2);
        }
        return $sum / $n;
    }

    public function derivative(array $y_true, array $y_pred): array
    {
        $n = count($y_true);
        $res = [];
        for ($i = 0; $i < $n; $i++) {
            $res[] = 2.0 * ($y_pred[$i] - $y_true[$i]) / $n;
        }
        return $res;
    }
}
