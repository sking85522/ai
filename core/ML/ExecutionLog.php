<?php
class ExecutionLog
{
    private string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?: (__DIR__ . '/../../storage/logs/agent_execution.jsonl');
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        if (!is_file($this->file)) {
            file_put_contents($this->file, '');
        }
    }

    public function append(array $row): void
    {
        $row['time'] = $row['time'] ?? date('c');
        @file_put_contents($this->file, json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}
