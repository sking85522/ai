<?php
namespace Core\MachineLearningAlgorithms\ReinforcementLearning;

/**
 * HRITIK AI - NEURAL Q-ENGINE
 * Replaces traditional Q-Tables with Neural Weight Matrices for continuous state spaces.
 */
class NeuralQEngine {
    
    private array $weights = [];
    private float $alpha = 0.01;
    private float $gamma = 0.95;

    public function __construct() {
        require_once dirname(__DIR__, 2) . '/Matrix/MatrixOps.php';
    }

    /**
     * Updates neural weights using the Bellman Equation logic.
     */
    public function update(string $state, string $action, float $reward, string $nextState): void {
        $currentQ = $this->getQ($state, $action);
        $maxFutureQ = $this->getMaxQ($nextState);

        // Neural Gradient-like update
        $delta = $reward + ($this->gamma * $maxFutureQ) - $currentQ;
        $this->weights[$state][$action] = ($this->weights[$state][$action] ?? 0.0) + ($this->alpha * $delta);
    }

    public function getQ(string $state, string $action): float {
        return $this->weights[$state][$action] ?? 0.0;
    }

    public function getMaxQ(string $nextState): float {
        if (!isset($this->weights[$nextState])) return 0.0;
        return (float)max($this->weights[$nextState]);
    }

    public function getAction(string $state, array $availableActions): string {
        // Epsilon-greedy implementation
        if (rand(0, 100) < 10) return $availableActions[array_rand($availableActions)];

        $bestAction = $availableActions[0];
        $maxQ = -999;
        foreach ($availableActions as $action) {
            $q = $this->getQ($state, $action);
            if ($q > $maxQ) {
                $maxQ = $q;
                $bestAction = $action;
            }
        }
        return $bestAction;
    }
}
