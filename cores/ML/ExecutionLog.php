<?php
namespace Core\ML;

class ExecutionLog {
    private string $logPath;

    public function __construct() {
        $this->logPath = dirname(__DIR__, 2) . '/storage/logs/ml_execution.log';
        if (!is_dir(dirname($this->logPath))) mkdir(dirname($this->logPath), 0777, true);
    }

    public function log(string $component, string $message): void {
        $entry = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $component, $message);
        file_put_contents($this->logPath, $entry, FILE_APPEND);
    }
}
