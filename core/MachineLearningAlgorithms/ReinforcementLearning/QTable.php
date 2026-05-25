<?php
namespace Core\MachineLearningAlgorithms\ReinforcementLearning;

class QTable {
    private array $table = [];
    private array $actions;

    public function __construct(array $actions) {
        $this->actions = $actions;
    }

    /**
     * Get Q-value for a state-action pair
     */
    public function getQValue(string $state, string $action): float {
        $this->ensureStateExists($state);
        return $this->table[$state][$action];
    }

    /**
     * Set Q-value for a state-action pair
     */
    public function setQValue(string $state, string $action, float $value): void {
        $this->ensureStateExists($state);
        $this->table[$state][$action] = $value;
    }

    /**
     * Get maximum Q-value for a specific state across all actions
     */
    public function getMaxQValue(string $state): float {
        $this->ensureStateExists($state);
        return max($this->table[$state]);
    }

    /**
     * Ensure the state is initialized in the Q-table
     */
    private function ensureStateExists(string $state): void {
        if (!isset($this->table[$state])) {
            $this->table[$state] = [];
            foreach ($this->actions as $action) {
                // Initialize optionally with 0 or small random numbers
                $this->table[$state][$action] = 0.0;
            }
        }
    }

    /**
     * Debug: Print table
     */
    public function getTableData(): array {
        return $this->table;
    }
}
