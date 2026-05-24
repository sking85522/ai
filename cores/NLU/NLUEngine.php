<?php
namespace Core\NLU;

/**
 * HRITIK AI - NEURAL NLU ENGINE
 * The core comprehension layer optimized for speed and bilingual understanding.
 */
class NLUEngine {
    
    private EntityExtractor $entityExtractor;
    private ContextTracker $contextTracker;
    private SemanticMatcher $semanticMatcher;
    private ?\Core\DL\NeuralNetwork $dlNet = null;

    public function __construct() {
        require_once __DIR__ . '/EntityExtractor.php';
        require_once __DIR__ . '/ContextTracker.php';
        require_once __DIR__ . '/SemanticMatcher.php';
        require_once __DIR__ . '/../DL/NeuralNetwork.php';
        
        $this->entityExtractor = new EntityExtractor();
        $this->contextTracker = new ContextTracker();
        $this->semanticMatcher = new SemanticMatcher();
    }

    /**
     * Deep understanding of user input.
     */
    public function understand(string $text, string $detectedIntent = 'unknown'): array {
        $text = $this->contextTracker->resolveReferences($text);
        $entities = $this->entityExtractor->extract($text);
        $isFollowUp = $this->contextTracker->isFollowUp($text);

        $enhancedIntent = $this->enhanceIntent($detectedIntent, $text, $entities, $isFollowUp);
        $confidence = $this->calculateConfidence($enhancedIntent, $entities, $text);

        $this->contextTracker->update($text, $enhancedIntent, $entities);

        return [
            'status' => 'success',
            'intent' => $enhancedIntent,
            'confidence' => round($confidence, 4),
            'entities' => $entities,
            'is_follow_up' => $isFollowUp,
            'context_summary' => $this->contextTracker->getContextSummary()
        ];
    }

    private function enhanceIntent(string $intent, string $text, array $entities, bool $isFollowUp): string {
        if (in_array($intent, ['identity', 'greeting', 'chat', 'farewell'], true)) {
            return $intent;
        }

        // High priority: Hinglish Question Detection
        if (preg_match('/\b(kya|kaun|kab|kahan|kaise|kyu|btao|dikhao|what|how|who|where|why)\b/i', $text)) {
            return 'informational';
        }

        if (!empty($entities['programming'])) return 'coding';
        
        return ($intent !== 'unknown') ? $intent : 'conversational';
    }

    private function calculateConfidence(string $intent, array $entities, string $text): float {
        if (!$this->dlNet) {
            $this->dlNet = new \Core\DL\NeuralNetwork(0.1);
            $this->dlNet->addLayer(3, 4, 'sigmoid');
            $this->dlNet->addLayer(4, 1, 'sigmoid');
        }

        $f1 = ($intent !== 'unknown') ? 0.9 : 0.3;
        $f2 = min(count($entities) / 5, 1.0);
        $f3 = min(strlen($text) / 100, 1.0);

        $out = $this->dlNet->predict([$f1, $f2, $f3]);
        return (float)($out[0] ?? 0.5);
    }
}
