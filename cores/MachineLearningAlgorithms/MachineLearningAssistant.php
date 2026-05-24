<?php
namespace Core\MachineLearningAlgorithms;

/**
 * MachineLearningAssistant
 * Central ML hub that routes requests to the correct algorithm subsystem
 * (Supervised, Unsupervised, Reinforcement Learning)
 */
class MachineLearningAssistant {

    /**
     * Route an ML task to the correct subsystem
     */
    public function route(string $task, array $params = []): string {
        $task = strtolower(trim($task));

        if (strpos($task, 'regression') !== false || strpos($task, 'classify') !== false || strpos($task, 'supervised') !== false) {
            return $this->runSupervised($params);
        }

        if (strpos($task, 'cluster') !== false || strpos($task, 'unsupervised') !== false || strpos($task, 'kmeans') !== false) {
            return $this->runUnsupervised($params);
        }

        if (strpos($task, 'rl') !== false || strpos($task, 'reinforcement') !== false || strpos($task, 'agent') !== false) {
            return $this->runRL($params);
        }

        return "ML Assistant could not identify the task type. Try: regression, clustering, or reinforcement learning.";
    }

    private function runSupervised(array $params): string {
        require_once dirname(__DIR__) . '/ML/LinearRegression.php';
        $lr = new \Core\ML\LinearRegression();
        
        $X = $params['X'] ?? [[1],[2],[3],[4],[5]];
        $y = $params['y'] ?? [2,4,6,8,10];

        $lr->fit($X, $y);
        $weights = $lr->getWeights();
        $wStr = implode(', ', array_map(fn($w) => round($w, 2), $weights));

        return "Supervised Learning (Linear Regression) executed. Learned weights: [{$wStr}].";
    }

    private function runUnsupervised(array $params): string {
        require_once __DIR__ . '/Unsupervisedlearning/UnsupervisedModels.php';
        $kmeans = new \Core\MachineLearningAlgorithms\UnsupervisedLearning\KMeansClustering();
        
        $data = $params['data'] ?? [[1,2],[1.5,1.8],[5,8],[8,8],[1,0.6],[9,11]];
        $k = $params['k'] ?? 2;
        
        $result = $kmeans->fit($data, $k);
        return $result;
    }

    private function runRL(array $params): string {
        require_once __DIR__ . '/ReinforcementLearning/ReinforcementLearningAssistant.php';
        $rl = new \Core\MachineLearningAlgorithms\ReinforcementLearning\ReinforcementLearningAssistant();
        
        $episodes = $params['episodes'] ?? 200;
        return $rl->runTraining($episodes);
    }
}
