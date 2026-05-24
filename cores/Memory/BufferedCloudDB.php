<?php
namespace Core\Memory;

/**
 * HRITIK AI - BUFFERED CLOUD DB
 * Buffers data locally to 'localstorage/' and syncs intelligently.
 */
class BufferedCloudDB {
    private \RemoteDB $remoteDb;
    private string $bufferDir;
    private int $bufferLimit;
    private int $syncRowLimit;

    public function __construct() {
        $this->bufferLimit = (int)(1.5 * 1024 * 1024);
        $this->syncRowLimit = 15;
        require_once dirname(__DIR__, 2) . '/online_db.php';
        $this->remoteDb = new \RemoteDB();
        $this->bufferDir = dirname(__DIR__, 2) . '/localstorage';
        
        if (!is_dir($this->bufferDir)) {
            mkdir($this->bufferDir, 0777, true);
        }
    }
    
    public function query(string $sql) {
        return $this->remoteDb->query($sql);
    }

    public function flushIfPending(string $table, int $minRows = 1): void {
        $bufferFile = $this->bufferDir . "/buffer_{$table}.jsonl";
        if (!is_file($bufferFile)) {
            return;
        }

        $lines = file($bufferFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines || count($lines) < $minRows) {
            return;
        }

        $this->flushBuffer($table);
    }

    private function ensureTableExists(string $table, array $columns): void {
        $colDefs = [];
        foreach ($columns as $col) {
            $colDefs[] = "`{$col}` LONGTEXT";
        }
        $colsStr = implode(', ', $colDefs);
        
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            {$colsStr},
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->remoteDb->query($sql);
    }

    public function bufferInsert(string $table, array $data): void {
        $bufferFile = $this->bufferDir . "/buffer_{$table}.jsonl";

        $this->ensureTableExists($table, array_keys($data));
        file_put_contents($bufferFile, json_encode($data) . "\n", FILE_APPEND);

        clearstatcache();
        $sizeReached = is_file($bufferFile) && filesize($bufferFile) >= $this->bufferLimit;
        $rowsReached = $this->countBufferedRows($bufferFile) >= $this->syncRowLimit;

        if ($sizeReached || $rowsReached) {
            $this->flushBuffer($table);
        }
    }

    public function flushBuffer(string $table): void {
        $bufferFile = $this->bufferDir . "/buffer_{$table}.jsonl";
        if (!file_exists($bufferFile)) {
            return;
        }

        $lines = file($bufferFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            return;
        }

        $batch = [];
        $columns = [];
        
        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if (!is_array($data)) {
                continue;
            }

            if (empty($columns)) {
                $columns = array_keys($data);
                $this->ensureTableExists($table, $columns);
            }

            $row = [];
            foreach ($columns as $col) {
                $row[] = "'" . addslashes((string)($data[$col] ?? '')) . "'";
            }
            $batch[] = "(" . implode(', ', $row) . ")";
        }

        if (empty($batch) || empty($columns)) {
            return;
        }

        $colsStr = "`" . implode('`, `', $columns) . "`";
        $valuesStr = implode(', ', $batch);
        $sql = "INSERT INTO `{$table}` ({$colsStr}) VALUES {$valuesStr}";
        
        $res = $this->remoteDb->query($sql);
        if (isset($res['status']) && $res['status'] === 'success') {
            unlink($bufferFile);
            return;
        }

        file_put_contents(
            $this->bufferDir . "/error_log.txt",
            date('Y-m-d H:i:s') . " - Flush Failed on {$table}: " . json_encode($res) . "\n",
            FILE_APPEND
        );
    }

    private function countBufferedRows(string $bufferFile): int {
        $lines = file($bufferFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $lines ? count($lines) : 0;
    }
}
