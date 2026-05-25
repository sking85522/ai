<?php
namespace Core\Memory;

class ConversationState {
    private string $state = 'idle';
    private array $activeParams = [];

    public function setState(string $newState, array $params = []): void {
        $this->state = $newState;
        $this->activeParams = $params;
    }

    public function getState(): string {
        return $this->state;
    }

    public function getParams(): array {
        return $this->activeParams;
    }

    public function is(string $state): bool {
        return $this->state === $state;
    }

    private string $key = 'ai_conversation_state';

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
        $_SESSION[$this->key] = $_SESSION[$this->key] ?? [
            'active_topic' => '',
            'active_intent' => '',
            'last_entities' => [],
            'last_resolved_input' => '',
            'updated_at' => date('c'),
        ];
    }

    public function get(): array
    {
        $state = $_SESSION[$this->key] ?? [];
        return is_array($state) ? $state : [];
    }

    public function update(array $patch): void
    {
        $state = $this->get();
        foreach ($patch as $k => $v) {
            $state[$k] = $v;
        }
        $state['updated_at'] = date('c');
        $_SESSION[$this->key] = $state;
    }
}
