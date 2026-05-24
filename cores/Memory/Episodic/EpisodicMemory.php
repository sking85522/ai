<?php
namespace Core\Memory\Episodic;

use Core\Memory\Storage\OnlineMemoryBridge;

/**
 * HRITIK AI - EPISODIC MEMORY
 * Manages chronological conversation history using the Online Memory Bridge.
 */
class EpisodicMemory {
    
    private OnlineMemoryBridge $bridge;

    public function __construct() {
        require_once __DIR__ . '/../Storage/OnlineMemoryBridge.php';
        $this->bridge = new OnlineMemoryBridge();
    }

    /**
     * Saves a conversation session to the cloud.
     */
    public function saveSession(string $sessionId, array $history): bool {
        return $this->bridge->save('episodic_session', $sessionId, $history);
    }

    /**
     * Recalls a conversation session from the cloud.
     */
    public function recallSession(string $sessionId): array {
        $data = $this->bridge->get('episodic_session', $sessionId);
        return $data ? json_decode($data, true) : [];
    }
}
