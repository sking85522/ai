<?php
namespace Core\MachineLearningAlgorithms\ReinforcementLearning;

require_once __DIR__ . '/QTable.php';
require_once __DIR__ . '/Agent.php';

class QTrainer {
    private Agent $agent;
    private QTable $qTable;
    
    // Hyperparameters
    private float $alpha = 0.1;  // Learning rate
    private float $gamma = 0.9;  // Discount factor (importance of future rewards)
    
    // Basic 1D Environment
    private int $goalState = 4;
    private array $actions = ['left', 'right'];

    public function __construct() {
        $this->qTable = new QTable($this->actions);
        $this->agent = new Agent($this->qTable, $this->actions, 0.2); // 20% exploration
    }

    /**
     * Train the agent over N episodes
     */
    public function train(int $episodes = 100): array {
        for ($i = 0; $i < $episodes; $i++) {
            $state = 0; // Start state
            
            while ($state !== $this->goalState) {
                // Agent picks action based on policy
                $stateStr = "s{$state}";
                $action = $this->agent->chooseAction($stateStr);
                
                // Environment Step
                $nextState = $state;
                if ($action === 'right') {
                    $nextState = min($state + 1, $this->goalState);
                } elseif ($action === 'left') {
                    $nextState = max($state - 1, 0); 
                }

                // Reward logic
                $reward = ($nextState === $this->goalState) ? 10.0 : -0.1; // Small penalty for wandering
                $nextStateStr = "s{$nextState}";

                // Bellman Equation Q-Update
                $currentQ = $this->qTable->getQValue($stateStr, $action);
                $maxFutureQ = $this->qTable->getMaxQValue($nextStateStr);
                
                $newQ = $currentQ + $this->alpha * ($reward + $this->gamma * $maxFutureQ - $currentQ);
                $this->qTable->setQValue($stateStr, $action, $newQ);

                $state = $nextState;
            }
        }
        
        return $this->qTable->getTableData();
    }
}
