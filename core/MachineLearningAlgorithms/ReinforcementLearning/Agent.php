<?php
namespace Core\MachineLearningAlgorithms\ReinforcementLearning;

class Agent {
    private QTable $qTable;
    private array $actions;
    private float $epsilon; // Exploration rate
    
    public function __construct(QTable $qTable, array $actions, float $epsilon = 0.1) {
        $this->qTable = $qTable;
        $this->actions = $actions;
        $this->epsilon = $epsilon;
    }

    /**
     * Choose an action using the Epsilon-Greedy policy
     */
    public function chooseAction(string $state): string {
        // Explore: random chance
        if ((mt_rand(1, 100) / 100) < $this->epsilon) {
            return $this->actions[array_rand($this->actions)];
        }

        // Exploit: Pick best known action
        $bestAction = $this->actions[0];
        $bestValue = -999999.0;

        foreach ($this->actions as $action) {
            $value = $this->qTable->getQValue($state, $action);
            if ($value > $bestValue) {
                $bestValue = $value;
                $bestAction = $action;
            }
        }

        return $bestAction;
    }

    public function getQTable(): QTable {
        return $this->qTable;
    }
}
