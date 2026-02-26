<?php
class ShortTermMemory
{
    private string $key = 'ai_short_term_memory';

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
        $_SESSION[$this->key] = $_SESSION[$this->key] ?? [];
    }

    public function add(array $interaction): void
    {
        $_SESSION[$this->key][] = $interaction;
        $_SESSION[$this->key] = array_slice($_SESSION[$this->key], -20);
    }

    public function recent(int $limit = 5): array
    {
        return array_slice($_SESSION[$this->key], -$limit);
    }
}
