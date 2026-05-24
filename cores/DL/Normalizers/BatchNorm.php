<?php
namespace Core\DL\Normalizers;

/**
 * HRITIK AI - BATCH NORMALIZATION LAYER
 * Accelerates training by normalizing the inputs to each layer.
 */
class BatchNorm {
    
    private float $epsilon = 1e-5;
    private float $momentum = 0.9;
    private array $runningMean = [];
    private array $runningVar = [];

    /**
     * Normalizes a batch of neural activations.
     */
    public function normalize(array $batch): array {
        $mean = array_sum($batch) / count($batch);
        $variance = array_reduce($batch, fn($carry, $item) => $carry + pow($item - $mean, 2), 0) / count($batch);
        
        $normalized = [];
        foreach ($batch as $val) {
            $normalized[] = ($val - $mean) / sqrt($variance + $this->epsilon);
        }
        
        return $normalized;
    }
}
