<?php
namespace Core\DL\Layers;

/**
 * HRITIK AI - RESIDUAL LAYER (SKIP CONNECTION)
 * Implements ResNet logic to prevent gradient vanishing in very deep networks.
 */
class ResidualLayer {
    
    /**
     * Adds the input (identity) directly to the output of a layer.
     */
    public function forward(array $identity, array $layerOutput): array {
        $result = [];
        foreach ($layerOutput as $i => $val) {
            $result[$i] = $val + ($identity[$i] ?? 0);
        }
        return $result;
    }
}
