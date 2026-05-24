<?php
namespace Core\Evaluation\Accuracy;

require_once dirname(__DIR__) . '/Metrics.php';
use Core\Evaluation\Metrics;

class AccuracyMetric {
    public function calculate(array $yTrue, array $yPred): float {
        return Metrics::accuracy($yTrue, $yPred);
    }
}
