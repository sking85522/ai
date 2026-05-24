<?php
namespace Core\ML;

/**
 * HRITIK AI - MASTER SUPERVISED LEARNER
 * Orchestrates all supervised learning tasks including Regression, Classification, and Fact-Learning.
 */
class SupervisedLearner {
    
    private array $coreKnowledge = [];
    private string $jsonPath;

    public function __construct() {
        $this->jsonPath = __DIR__ . '/../../localstorage/data/core_knowledge.json';
        $this->loadCore();
    }

    private function loadCore() {
        if (file_exists($this->jsonPath)) {
            $this->coreKnowledge = json_decode(file_get_contents($this->jsonPath), true) ?: [];
        }
    }

    /**
     * Train a specific model type (Regression, Classifier)
     */
    public function train(string $type, array $X, array $y) {
        if ($type === 'regression') {
            require_once __DIR__ . '/LinearRegression.php';
            $model = new LinearRegression();
            $model->fit($X, $y);
            return $model;
        }
        return null;
    }

    /**
     * Returns the raw core knowledge data (Required by Engine).
     */
    public function getCoreData(): array {
        return $this->coreKnowledge;
    }

    /**
     * Quick Retrieval from learned core knowledge.
     */
    public function findFact(string $prompt): ?string {
        $prompt = strtolower(trim($prompt));
        foreach ($this->coreKnowledge as $entry) {
            if (strtolower(trim($entry['question'] ?? '')) === $prompt) return $entry['answer'];
        }
        return null;
    }

    /**
     * Teach the AI a new fact.
     */
    public function teach(string $prompt, string $answer): void {
        $this->coreKnowledge[] = ['question' => $prompt, 'answer' => $answer];
        if (!is_dir(dirname($this->jsonPath))) @mkdir(dirname($this->jsonPath), 0777, true);
        file_put_contents($this->jsonPath, json_encode($this->coreKnowledge, JSON_PRETTY_PRINT));
    }
}
