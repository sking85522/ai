<?php
namespace Core\GenerativeAI\Optimization;

/**
 * HRITIK AI - LATENCY OPTIMIZER
 * Dynamically adjusts neural depth to ensure responses are delivered within time constraints.
 */
class LatencyOptimizer {
    
    private float $startTime;

    public function start(): void {
        $this->startTime = microtime(true);
    }

    /**
     * Checks if the generation is taking too long and suggests pruning.
     */
    public function shouldPrune(): bool {
        $elapsed = microtime(true) - $this->startTime;
        return $elapsed > 1.5; // Prune if > 1.5 seconds
    }
}
