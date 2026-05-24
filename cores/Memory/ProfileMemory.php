<?php
namespace Core\Memory;

require_once __DIR__ . '/FileMemoryStore.php';

class ProfileMemory {
    private FileMemoryStore $store;
    private array $profile = [];

    public function __construct() {
        $this->store = new FileMemoryStore();
    }

    public function load(string $userId): void {
        $this->profile = $this->store->get($userId, 'profiles');
    }

    public function get(string $key): mixed {
        return $this->profile[$key] ?? null;
    }

    public function set(string $userId, string $key, $value): void {
        $this->profile[$key] = $value;
        $this->store->set($userId, $this->profile, 'profiles');
    }

    public function getAll(): array {
        return $this->profile;
    }
    public function save(string $userId): void {
        $this->store->set($userId, $this->profile, 'profiles');
    }
}
