<?php
namespace Core\DL\Normalizers;

/**
 * HRITIK AI - LAYER NORMALIZATION
 * Normalizes activations within a single layer, essential for Transformer-based models.
 */
class LayerNorm {
    
    private float $epsilon = 1e-6;

    /**
     * Normalizes a single layer's output.
     */
    public function normalize(array $activations): array {
        $n = count($activations);
        if ($n === 0) return [];

        $mean = array_sum($activations) / $n;
        $variance = array_reduce($activations, fn($carry, $item) => $carry + pow($item - $mean, 2), 0) / $n;
        
        return array_map(fn($val) => ($val - $mean) / sqrt($variance + $this->epsilon), $activations);
    }
}
