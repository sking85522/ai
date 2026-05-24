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
}
