<?php
namespace Core\Learning;

/**
 * HRITIK AI - REINFORCEMENT LEARNER
 * Handles user feedback and improves responses based on 'rewards' and 'penalties'.
 */
class ReinforcementLearner {
    
    private string $feedbackPath;

    public function __construct() {
        $this->feedbackPath = __DIR__ . '/../../localstorage/data/reinforcement_feedback.json';
    }

    /**
     * Records feedback for a specific prompt/response pair.
     */
    public function recordFeedback(string $prompt, string $response, string $feedbackType): void {
        $feedback = $this->loadFeedback();
        
        $feedback[] = [
            'timestamp' => time(),
            'prompt' => $prompt,
            'response' => $response,
            'type' => ($feedbackType === 'positive' || $feedbackType === 'correct') ? 'reward' : 'penalty',
            'weight' => ($feedbackType === 'positive') ? 1 : -1
        ];

        file_put_contents($this->feedbackPath, json_encode($feedback, JSON_PRETTY_PRINT));
    }

    /**
     * Returns the 'Correction' if the user previously corrected the AI.
     */
    public function getCorrection(string $prompt): ?string {
        $feedback = $this->loadFeedback();
        foreach (array_reverse($feedback) as $entry) {
            if ($entry['prompt'] === $prompt && $entry['type'] === 'reward') {
                return $entry['response'];
            }
        }
        return null;
    }

    private function loadFeedback(): array {
        if (file_exists($this->feedbackPath)) {
            return json_decode(file_get_contents($this->feedbackPath), true) ?: [];
        }
        return [];
    }
}
