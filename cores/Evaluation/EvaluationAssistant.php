<?php
namespace Core\Evaluation;

require_once __DIR__ . '/MetricsSuite.php';

class EvaluationAssistant {
    private MetricsSuite $suite;

    public function __construct() {
        $this->suite = new MetricsSuite();
    }

    /**
     * Evaluates a model's performance on a single task.
     */
    public function evaluatePerformance(array $testData): array {
        $yTrue = $testData['y_true'] ?? [];
        $yPred = $testData['y_pred'] ?? [];
        
        return $this->suite->getFullReport($yTrue, $yPred);
    }

    /**
     * Runs evaluation on a full suite of benchmarks from a JSON file.
     */
    public function runSuite(array $suiteData): array {
        $results = [];
        $benchmarks = $suiteData['benchmarks'] ?? [];
        
        foreach ($benchmarks as $bm) {
            $results[$bm['id']] = [
                'name' => $bm['name'],
                'report' => $this->evaluatePerformance($bm)
            ];
        }
        
        return [
            'total_benchmarks' => count($benchmarks),
            'results' => $results,
            'timestamp' => time()
        ];
    }

    public function getStatus(): string {
        return "Evaluation Assistant Online: Ready for model benchmarking.";
    }
}
