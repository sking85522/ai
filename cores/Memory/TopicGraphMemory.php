<?php
namespace Core\Memory;

class TopicGraphMemory {
    private array $graph = [];

    /**
     * Connect two topics.
     */
    public function connect(string $topicA, string $topicB): void {
        $this->graph[$topicA][] = $topicB;
        $this->graph[$topicB][] = $topicA;
        $this->graph[$topicA] = array_unique($this->graph[$topicA]);
        $this->graph[$topicB] = array_unique($this->graph[$topicB]);
    }

    /**
     * Get related topics for a given term.
     */
    public function getRelated(string $topic): array {
        return $this->graph[$topic] ?? [];
    }
}
