<?php
namespace Core\Memory;

require_once __DIR__ . '/FileMemoryStore.php';

class LongTermMemory {
    private FileMemoryStore $store;

    public function __construct() {
        $this->store = new FileMemoryStore();
    }

    /**
     * Archive the final state of a session.
     */
    public function archive(string $sessionId, array $fullHistory): void {
        $this->store->set($sessionId, $fullHistory, 'long_term');
    }

    /**
     * Search past sessions for a keyword (Functional Retrieval).
     */
    public function search(string $query): array {
        $allSessions = $this->store->getAllNamespace('long_term');
        $results = [];
        $queryWords = explode(' ', strtolower($query));

        foreach ($allSessions as $sessionId => $history) {
            foreach ($history as $message) {
                $content = strtolower($message['content'] ?? '');
                $matchCount = 0;
                foreach ($queryWords as $word) {
                    if (strlen($word) > 3 && strpos($content, $word) !== false) {
                        $matchCount++;
                    }
                }
                
                if ($matchCount > 0) {
                    $results[] = [
                        'content' => $message['content'],
                        'session' => $sessionId,
                        'relevance' => $matchCount
                    ];
                }
            }
        }

        // Sort by relevance
        usort($results, fn($a, $b) => $b['relevance'] <=> $a['relevance']);
        return array_slice($results, 0, 5);
    }
}
