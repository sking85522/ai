<?php
namespace Core\Memory;

class ShortTermMemory {
    private array $buffer = [];
    private int $limit;

    public function __construct(int $limit = 10) {
        $this->limit = $limit;
    }

    /**
     * Store a new interaction in the sliding window.
     */
    public function add(string $role, string $content): void {
        $this->buffer[] = ['role' => $role, 'content' => $content, 'time' => time()];
        if (count($this->buffer) > $this->limit) {
            array_shift($this->buffer);
        }
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
}
