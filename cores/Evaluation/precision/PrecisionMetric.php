<?php
namespace Core\Evaluation\Precision;

require_once dirname(__DIR__) . '/Metrics.php';
use Core\Evaluation\Metrics;

class PrecisionMetric {
    public function calculate(array $yTrue, array $yPred): float {
        return Metrics::precision($yTrue, $yPred);
    }
}
