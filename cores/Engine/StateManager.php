<?php
namespace Core\Engine;

/**
 * HRITIK AI - STATE MANAGER
 * Handles User Profile, Short Term Memory, and Conversation State.
 */
class StateManager {
    private $profile;
    private $stm;
    private $memory;
    private $context;
    private \Core\Memory\BufferedCloudDB $cloudDb;

    public function __construct($profile, $stm, $memory, $context) {
        $this->profile = $profile;
        $this->stm = $stm;
        $this->memory = $memory;
        $this->context = $context;
        
        require_once dirname(__DIR__) . '/Memory/BufferedCloudDB.php';
        $this->cloudDb = new \Core\Memory\BufferedCloudDB();
    }

    public function initializeSession(string $sessionId, string $prompt) {
        $this->profile->load($sessionId);
        $this->hydrateShortTermMemory($sessionId);
        $this->context->update($prompt);
        $this->stm->add('user', $prompt);
    }

    public function recordInteraction(string $sessionId, string $prompt, string $mood, bool $hasFile) {
        $interaction = [
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $prompt . ($hasFile ? ' [Dataset Attached]' : ''),
            'mood' => $mood,
            'timestamp' => time()
        ];
        
        // Local Save
        $this->memory->append($sessionId, $interaction);
        
        // Cloud Buffer
        $this->cloudDb->bufferInsert('user_interactions', $interaction);
    }

    public function recordAssistantResponse(string $sessionId, string $response): void {
        $interaction = [
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $response,
            'timestamp' => time()
        ];

        $this->stm->add('assistant', $response);
        $this->memory->append($sessionId, $interaction);
    }

    public function getRecentHistory(string $sessionId, int $limit = 6): array {
        $history = $this->memory->get($sessionId);
        return array_slice($history, -$limit);
    }

    public function savePersonalFacts(string $sessionId, array $facts) {
        foreach ($facts as $key => $val) {
            $this->profile->set($sessionId, $key, $val);
            
            // Cloud Buffer for facts
            $this->cloudDb->bufferInsert('user_facts', [
                'session_id' => $sessionId,
                'fact_key' => $key,
                'fact_value' => is_array($val) ? json_encode($val) : $val
            ]);
        }
        $this->profile->save($sessionId);
    }

    private function hydrateShortTermMemory(string $sessionId): void {
        if (!empty($this->stm->getBuffer())) {
            return;
        }

        $history = array_slice($this->memory->get($sessionId), -6);
        foreach ($history as $item) {
            if (!empty($item['role']) && !empty($item['content'])) {
                $this->stm->add((string)$item['role'], (string)$item['content']);
            }
        }
    }
}
