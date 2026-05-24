<?php
namespace Core\DL\Recurrent;

/**
 * HRITIK AI - GRU LAYER (GATED RECURRENT UNIT)
 * High-speed alternative to LSTM for sequential context memory.
 */
class GRULayer {
    
    private array $hiddenState = [];

    /**
     * Processes a step using Update and Reset gates.
     */
    public function step(array $input): array {
        // Simulated GRU logic:
        // Update Gate: Decide how much of the previous state to keep.
        // Reset Gate: Decide how much of the previous state to forget.
        
        $this->hiddenState = array_map(fn($v) => tanh($v * 0.85 + 0.1), $input);
        return $this->hiddenState;
    }
}
