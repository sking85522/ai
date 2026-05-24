<?php
namespace Core\MachineLearningAlgorithms\ReinforcementLearning;

require_once __DIR__ . '/QTrainer.php';

/**
 * ReinforcementLearningAssistant
 * Top-level orchestrator that the Engine calls for RL tasks.
 * Initializes QTrainer, runs training, and returns human-readable results.
 */
class ReinforcementLearningAssistant {
    
    private int $defaultEpisodes = 200;

    /**
     * Run a full RL training demonstration and return a summary
     */
    public function runTraining(int $episodes = null): string {
        $episodes = $episodes ?? $this->defaultEpisodes;

        $trainer = new QTrainer();
        $qTable = $trainer->train($episodes);

        // Format Q-Table into readable string
        $tableStr = "";
        foreach ($qTable as $state => $actions) {
            $lVal = round($actions['left'], 3);
            $rVal = round($actions['right'], 3);
            $best = ($rVal > $lVal) ? 'Right' : 'Left';
            $tableStr .= "{$state}[L:{$lVal} R:{$rVal} Best:{$best}] ";
        }

        // Calculate if agent learned the optimal policy
        $optimalCount = 0;
        $totalStates = count($qTable);
        foreach ($qTable as $state => $actions) {
            if ($state === 's4') continue; // goal state
            if ($actions['right'] > $actions['left']) $optimalCount++;
        }
        
        $accuracy = $totalStates > 1 ? round(($optimalCount / ($totalStates - 1)) * 100, 1) : 0;

        return "Reinforcement Learning Training Complete! " .
               "Agent trained for {$episodes} episodes in a 1D Grid World (5 states). " .
               "Bellman Equation applied with alpha=0.1, gamma=0.9. " .
               "Policy Accuracy: {$accuracy}% of states correctly map to optimal action. " .
               "Q-Table: {$tableStr}";
    }

    /**
     * Quick status check
     */
    public function getStatus(): string {
        return "RL Module Online. Q-Learning Agent ready for training on Grid, Maze and custom environments.";
    }
}
