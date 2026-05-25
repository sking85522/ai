<?php
namespace Core\Memory;

class ShortTermMemory {
    private array $buffer = [];
    private int $limit;
    private string $key = 'ai_short_term_memory';

    public function __construct(int $limit = 10) {
        $this->limit = $limit;
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
        $_SESSION[$this->key] = $_SESSION[$this->key] ?? [];
    }

    /**
     * Store a new interaction in the sliding window.
     */
    public function add(string $role, string $content,array $interaction): void {
        $this->buffer[] = ['role' => $role, 'content' => $content, 'time' => time()];
        if (count($this->buffer) > $this->limit) {
            array_shift($this->buffer);
        }
        $_SESSION[$this->key][] = $interaction;
        $_SESSION[$this->key] = array_slice($_SESSION[$this->key], -20);
    }
 

    /**
     * Returns the recent context as a flat string or array.
     */
    public function getSummary(): string {
        $summary = "";
        foreach ($this->buffer as $msg) {
            $summary .= $msg['role'] . ": " . $msg['content'] . "\n";
        }
        return $summary;
    }

    public function getBuffer(): array {
        return $this->buffer;
    }

    public function clear(): void {
        $this->buffer = [];
    }

    

   

    public function recent(int $limit = 5): array
    {
        return array_slice($_SESSION[$this->key], -$limit);
    }
}