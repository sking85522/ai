<?php
namespace Core\ML;

class ExecutionLog {
    private string $logPath;
    private string $file;

  


    public function __construct(?string $file = null)
    {
        $this->logPath = dirname(__DIR__, 2) . '/storage/logs/ml_execution.log';
        if (!is_dir(dirname($this->logPath))) mkdir(dirname($this->logPath), 0777, true);
        $this->file = $file ?: (__DIR__ . '/../../storage/logs/agent_execution.jsonl');
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        if (!is_file($this->file)) {
            file_put_contents($this->file, '');
        }
    }

      public function log(string $component, string $message): void {
        $entry = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $component, $message);
        file_put_contents($this->logPath, $entry, FILE_APPEND);
    }

    public function append(array $row): void
    {
        $row['time'] = $row['time'] ?? date('c');
        @file_put_contents($this->file, json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}