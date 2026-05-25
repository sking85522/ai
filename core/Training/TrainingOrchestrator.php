<?php
namespace Core\Training;

require_once __DIR__ . '/Optimizers/SGD.php';
require_once __DIR__ . '/LossCalculation/CrossEntropy.php';

use Core\Training\LossCalculation\CrossEntropy;
use Core\Training\Optimizers\SGD;

class TrainingOrchestrator {
    private array $logs = [];

    public function trainModel($model, array $dataset, int $epochs = 10, float $lr = 0.01): array {
        $optimizer = new SGD($lr);
        $lossFn = new CrossEntropy();

        $this->logs = ["Starting training: {$epochs} epochs at learning rate {$lr}"];
        $finalAccuracy = 0.0;

        if (empty($dataset)) {
            return [
                'status' => 'error',
                'accuracy' => 0.0,
                'logs' => ['Training dataset is empty.'],
            ];
        }

        for ($e = 1; $e <= $epochs; $e++) {
            $totalLoss = 0.0;
            $correct = 0;

            foreach ($dataset as $sample) {
                $prediction = $model->forward($sample['input']);
                $target = $sample['target'];

                $loss = $lossFn->calculate($target, $prediction);
                $totalLoss += $loss;
                $gradients = $this->calculateGradients($target, $prediction);

                if (method_exists($model, 'getLayers')) {
                    foreach ($model->getLayers() as $layer) {
                        if (isset($layer->weights) && is_array($layer->weights)) {
                            $optimizer->update($layer->weights, $gradients);
                        }
                    }
                }

                if ($this->isCorrect($target, $prediction)) {
                    $correct++;
                }
            }

            $avgLoss = $totalLoss / count($dataset);
            $finalAccuracy = ($correct / count($dataset)) * 100;
            $this->logs[] = "Epoch {$e}/{$epochs} | Loss: " . round($avgLoss, 4) . " | Acc: " . round($finalAccuracy, 1) . "%";
        }

        return [
            'status' => 'Training Completed',
            'accuracy' => $finalAccuracy,
            'logs' => $this->logs,
        ];
    }

    private function calculateGradients(array $target, array $prediction): array {
        $grads = [];
        foreach ($target as $i => $t) {
            $grads[] = (float)$t - (float)($prediction[$i] ?? 0);
        }
        return $grads;
    }

    private function isCorrect(array $target, array $prediction): bool {
        $tIdx = array_search(max($target), $target, true);
        $pIdx = array_search(max($prediction), $prediction, true);
        return $tIdx === $pIdx;
    }
}
