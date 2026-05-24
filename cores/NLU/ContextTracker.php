<?php
namespace Core\NLU;

/**
 * Context Tracker
 * Maintains multi-turn conversation context for coherent dialogues.
 * Tracks topic, entities, intent history, and conversation flow.
 */
class ContextTracker {

    private array $intentHistory = [];
    private array $topicStack = [];
    private array $entityMemory = [];
    private array $turnHistory = [];
    private int $maxTurns = 20;
    private ?string $currentTopic = null;
    private ?string $dominantEntity = null;
    private \Core\Memory\TopicGraphMemory $topicGraph;

    public function __construct() {
        require_once __DIR__ . '/../Memory/TopicGraphMemory.php';
        $this->topicGraph = new \Core\Memory\TopicGraphMemory();
    }

    /**
     * Update context with new turn data
     */
    public function update(string $prompt, string $intent, array $entities = []): void {
        // Track intent history
        $this->intentHistory[] = $intent;
        if (count($this->intentHistory) > $this->maxTurns) {
            array_shift($this->intentHistory);
        }

        // Track turn
        $this->turnHistory[] = [
            'prompt' => $prompt,
            'intent' => $intent,
            'entities' => $entities,
            'time' => time()
        ];
        if (count($this->turnHistory) > $this->maxTurns) {
            array_shift($this->turnHistory);
        }

        // Update topic from entities and build Topic Graph Memory
        $keywords = $entities['keywords'] ?? [];
        if (!empty($keywords)) {
            $prevTopic = $this->currentTopic;
            $this->currentTopic = reset($keywords); // Get first keyword
            $this->topicStack[] = $this->currentTopic;
            
            // Link new topic to previous topic in the graph
            if ($prevTopic && $prevTopic !== $this->currentTopic) {
                $this->topicGraph->connect($prevTopic, $this->currentTopic);
            }
            
            if (count($this->topicStack) > 10) {
                array_shift($this->topicStack);
            }
        }

        // Track named entities for co-reference resolution
        foreach (['names', 'locations', 'years'] as $type) {
            if (!empty($entities[$type])) {
                foreach ($entities[$type] as $entity) {
                    $this->entityMemory[$type] = $entity;
                }
            }
        }

        // Set dominant entity (most recently mentioned significant entity)
        if (!empty($entities['names'])) {
            $this->dominantEntity = reset($entities['names']);
        } elseif (!empty($entities['locations'])) {
            $this->dominantEntity = reset($entities['locations']);
        }
    }

    /**
     * Resolve pronouns and references using context
     * "uski population kya hai?" → if last entity was "India", resolves to "India ki population kya hai?"
     */
    public function resolveReferences(string $prompt): string {
        $lower = strtolower($prompt);
        
        // Hindi pronouns
        $pronouns = ['uska', 'uski', 'uske', 'iska', 'iski', 'iske', 'ye', 'wo', 'uss', 'is',
                      'its', 'their', 'his', 'her', 'that', 'those', 'this'];
        
        $hasReference = false;
        foreach ($pronouns as $pronoun) {
            if (preg_match('/\b' . preg_quote($pronoun) . '\b/i', $lower)) {
                $hasReference = true;
                break;
            }
        }

        if ($hasReference && $this->dominantEntity) {
            // Replace pronoun with the actual entity
            foreach ($pronouns as $pronoun) {
                $prompt = preg_replace('/\b' . preg_quote($pronoun) . '\b/i', $this->dominantEntity, $prompt, 1);
            }
        }

        return $prompt;
    }

    /**
     * Check if user is continuing a topic (follow-up question)
     */
    public function isFollowUp(string $prompt): bool {
        $lower = strtolower($prompt);
        $followUpMarkers = [
            'aur', 'or', 'and', 'also', 'bhi', 'phir', 'fir', 'next',
            'iske baad', 'aage', 'agge', 'btao', 'batao', 'more',
            'continue', 'then', 'what about', 'how about', 'tell more'
        ];

        foreach ($followUpMarkers as $marker) {
            if (strpos($lower, $marker) !== false && strlen($lower) < 50) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get conversation context summary for response generation
     */
    public function getContextSummary(): array {
        return [
            'current_topic' => $this->currentTopic,
            'dominant_entity' => $this->dominantEntity,
            'recent_intents' => array_slice($this->intentHistory, -3),
            'topic_stack' => array_slice($this->topicStack, -5),
            'entity_memory' => $this->entityMemory,
            'topic_connections' => $this->currentTopic ? $this->topicGraph->getRelated($this->currentTopic) : [],
            'turn_count' => count($this->turnHistory),
            'last_prompt' => !empty($this->turnHistory) ? end($this->turnHistory)['prompt'] : null
        ];
    }

    /**
     * Get last N turns for context window
     */
    public function getRecentTurns(int $n = 3): array {
        return array_slice($this->turnHistory, -$n);
    }

    /**
     * Get dominant intent (most common in recent turns)
     */
    public function getDominantIntent(): ?string {
        $recent = array_slice($this->intentHistory, -5);
        if (empty($recent)) return null;

        $counts = array_count_values($recent);
        arsort($counts);
        return array_key_first($counts);
    }

    /**
     * Reset context (new conversation)
     */
    public function reset(): void {
        $this->intentHistory = [];
        $this->topicStack = [];
        $this->entityMemory = [];
        $this->turnHistory = [];
        $this->currentTopic = null;
        $this->dominantEntity = null;
    }
}
