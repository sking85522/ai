<?php
namespace Core\DL;

use Core\DL\Layers\DenseLayer;
use Core\DL\Optimizers\AdamOptimizer;
use Core\DL\Activation\NeuralActivation;

/**
 * HRITIK AI - SUPREME NEURAL ENGINE
 * A modular deep learning orchestrator for high-complexity pattern recognition.
 */
class NeuralEngine {
    
    private array $layers = [];
    private AdamOptimizer $optimizer;

    public function __construct(array $structure, float $lr = 0.001) {
        require_once __DIR__ . '/Layers/DenseLayer.php';
        require_once __DIR__ . '/Optimizers/AdamOptimizer.php';
        require_once __DIR__ . '/Activation/NeuralActivation.php';

        $this->optimizer = new AdamOptimizer($lr);
        
        // Build the neural structure
        for ($i = 0; $i < count($structure) - 1; $i++) {
            $this->layers[] = new DenseLayer($structure[$i], $structure[$i+1]);
        }
    }

    /**
     * Predicts the output for a given input.
     */
    public function predict(array $inputs): array {
        $current = $inputs;
        foreach ($this->layers as $idx => $layer) {
            $current = $layer->forward($current);
            // Apply ReLU for hidden layers, Softmax for last layer
            if ($idx < count($this->layers) - 1) {
                $current = array_map([NeuralActivation::class, 'relu'], $current);
            } else {
                $current = NeuralActivation::softmax($current);
            }
        }
        return $current;
    }
}
