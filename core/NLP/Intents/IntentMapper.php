<?php
namespace Core\NLP\Intents;

/**
 * HRITIK AI - SEMANTIC INTENT MAPPER (DYNAMIC EDITION)
 * 
 * Identifies user intent using patterns retrieved from the neural knowledge base.
 */
class IntentMapper {
    
    /** @var array Shared cache for intent patterns */
    private static array $intentCache = [];

    /** @var array Shared cache for compiled regex patterns */
    private static array $compiledRegexCache = [];

    private static ?TrainableIntentClassifier $trainedClassifier = null;

    /**
     * Maps a prompt to the most likely intent based on neural patterns.
     * 
     * @param string $text Cleaned user input
     * @return string Detected intent slug
     */
    public function map(string $text): string {
        $intents = $this->getIntents();
        
        if (empty(self::$compiledRegexCache)) {
            foreach ($intents as $intentName => $patterns) {
                if (empty($patterns)) continue;
                // Pre-compile into a single optimized regex with alternation
                $escapedPatterns = array_map(function($p) { return preg_quote($p, '/'); }, $patterns);
                self::$compiledRegexCache[$intentName] = '/\b(' . implode('|', $escapedPatterns) . ')\b/i';
            }
        }
        
        foreach (self::$compiledRegexCache as $intentName => $regex) {
            if (preg_match($regex, $text)) return $intentName;
        }

        $trained = $this->getTrainedClassifier();
        if ($trained) {
            $prediction = $trained->predict($text);
            if (($prediction['intent'] ?? 'unknown') !== 'unknown') {
                return (string)$prediction['intent'];
            }
        }

        return 'general_chat';
    }

    /**
     * Fetches intent patterns from the Knowledge Base (DB).
     * 
     * @return array Map of intent slugs to pattern arrays
     */
    private function getIntents(): array {
        if (!empty(self::$intentCache)) return self::$intentCache;

        global $db;
        
        if (!isset($db) || $db === null) {
            // Fallback to basic intents if DB is not available
            return [
                'greeting' => ['hi', 'hello', 'hey', 'namaste', 'hlo'],
                'identity' => ['who are you', 'kaun ho', 'tera naam'],
                'tool_use' => ['run command', 'execute', 'read file', 'check code', 'scan project', 'system check', 'calculate', 'solve', 'plus', 'minus', 'multiply']
            ];
        }

        $results = $db->query("SELECT sub_category, k_key FROM neural_knowledge WHERE category = 'intent'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                $intent = $row['sub_category'];
                $pattern = $row['k_key'];
                
                if (!isset(self::$intentCache[$intent])) self::$intentCache[$intent] = [];
                self::$intentCache[$intent][] = $pattern;
            }
        }

        return self::$intentCache;
    }

    private function getTrainedClassifier(): ?TrainableIntentClassifier {
        if (self::$trainedClassifier instanceof TrainableIntentClassifier) {
            return self::$trainedClassifier;
        }

        self::$trainedClassifier = TrainableIntentClassifier::load();
        return self::$trainedClassifier;
    }
}
