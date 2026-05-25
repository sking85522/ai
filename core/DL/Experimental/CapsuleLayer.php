<?php
namespace Core\DL\Experimental;

/**
 * HRITIK AI - NEURAL CAPSULE LAYER
 * Implements "Capsules" to preserve hierarchical relationships between features.
 */
class CapsuleLayer {
    
    private array $capsules = [];

    public function __construct(int $numCapsules, int $dim) {
        for ($i = 0; $i < $numCapsules; $i++) {
            $this->capsules[$i] = array_fill(0, $dim, 0.0);
        }
    }

    /**
     * Executes dynamic routing between capsules (simplified).
     */
    public function route(array $votes): array {
        // Dynamic routing is a complex iterative process
        // Here we simulate the consensus building between capsules
        return array_map(fn($v) => $this->squash($v), $votes);
    }

    private function squash(array $vector): array {
        $squaredNorm = array_sum(array_map(fn($v) => $v*$v, $vector));
        $scale = $squaredNorm / (1 + $squaredNorm) / sqrt($squaredNorm + 1e-9);
        return array_map(fn($v) => $v * $scale, $vector);
    }
}
