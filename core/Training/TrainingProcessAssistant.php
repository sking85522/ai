<?php
namespace Core\Training;

require_once __DIR__ . '/TrainingOrchestrator.php';

class TrainingProcessAssistant {
    private TrainingOrchestrator $orchestrator;

    public function __construct() {
        $this->orchestrator = new TrainingOrchestrator();
    }

    public function startTraining(string $datasetPath, $model): array {
        if (!file_exists($datasetPath)) {
            return ['error' => 'Dataset not found at ' . $datasetPath];
        }

        $dataset = json_decode(file_get_contents($datasetPath), true);
        if (!is_array($dataset)) {
            return ['error' => 'Dataset is not valid JSON.'];
        }

        $trainingSet = $this->prepareData($dataset);
        return $this->orchestrator->trainModel($model, $trainingSet, 20, 0.05);
    }

    private function prepareData(array $data): array {
        if (isset($data['benchmarks']) && is_array($data['benchmarks'])) {
            return $data['benchmarks'];
        }

        return $data;
    }

    public function getStatus(): string {
        return "Training Engine Online: Advanced backpropagation and optimizers ready.";
    }
}
