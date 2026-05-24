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
}
