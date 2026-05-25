<?php
namespace Core\Memory;

class ContextManager {
    private ?string $currentTopic = null;
    private array $entities = [];

    /**
     * Updates context based on a new prompt.
     */
    public function update(string $prompt): void {
        // Simple entity/topic extraction (demo)
        if (preg_match('/(math|vision|data|rl|neural)/i', $prompt, $matches)) {
            $this->currentTopic = strtolower($matches[1]);
        }
        
        // Track unique words as potential entities
        $words = explode(' ', strtolower($prompt));
        foreach ($words as $word) {
            if (strlen($word) > 5) $this->entities[] = $word;
        }
        $this->entities = array_unique(array_slice($this->entities, -20));
    }

    public function getCurrentTopic(): ?string {
        return $this->currentTopic;
    }

    public function getActiveEntities(): array {
        return $this->entities;
    }

    public function getContextSummary(): string {
        $topic = $this->currentTopic ?? 'General';
        $entityCount = count($this->entities);
        return "Focus: <b>{$topic}</b> | Entities: {$entityCount}";
    }

    public function buildContext(array $tokens, array $recentInteractions, array $longTermHint): array
    {
        return [
            'tokens' => $tokens,
            'recent' => $recentInteractions,
            'memory_hint' => $longTermHint,
            'keyword_focus' => array_slice(array_values(array_unique($tokens)), 0, 5),
        ];
    }
}