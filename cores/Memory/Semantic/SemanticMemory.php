<?php
namespace Core\Memory\Semantic;

use Core\Memory\Storage\OnlineMemoryBridge;

/**
 * HRITIK AI - SEMANTIC MEMORY
 * Manages general facts, user profile data, and relationships using the Online Memory Bridge.
 */
class SemanticMemory {
    
    private OnlineMemoryBridge $bridge;

    public function __construct() {
        require_once __DIR__ . '/../Storage/OnlineMemoryBridge.php';
        $this->bridge = new OnlineMemoryBridge();
    }

    /**
     * Learns a new fact and saves it to the cloud.
     */
    public function learn(string $key, string $fact): bool {
        return $this->bridge->save('semantic_fact', $key, $fact);
    }

    /**
     * Recalls a specific fact from the cloud.
     */
    public function recall(string $key): ?string {
        return $this->bridge->get('semantic_fact', $key);
    }
}
