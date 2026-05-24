<?php
/**
 * HRITIK AI - LOCAL DATABASE COMPATIBILITY LAYER
 *
 * The engine expects a global $db object with a query($sql) method.  When the
 * original online database file is missing or unavailable, this lightweight
 * JSON-backed adapter keeps console.php and api.php running.
 */

class HritikLocalDB {
    private string $path;
    private array $data = [
        'neural_memory' => [],
        'neural_knowledge' => [],
        'neural_history' => [],
        'neural_feedback_log' => [],
    ];

    public function __construct(?string $path = null) {
        $this->path = $path ?: __DIR__ . '/storage/local_db.json';
        $this->load();
    }

    public function query(string $sql): array {
        $sql = trim($sql);
        if ($sql === '') {
            return ['status' => 'success', 'data' => []];
        }

        try {
            if (preg_match('/^SELECT\s+COUNT\(\*\)\s+as\s+total\s+FROM\s+(\w+)/i', $sql, $m)) {
                $table = $m[1];
                return ['status' => 'success', 'data' => [['total' => count($this->data[$table] ?? [])]]];
            }

            if (preg_match('/^SELECT\s+/i', $sql)) {
                return $this->select($sql);
            }

            if (preg_match('/^(INSERT|REPLACE)\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES\s*\((.+)\)/is', $sql, $m)) {
                return $this->insert($m[2], $this->columns($m[3]), $this->values($m[4]));
            }

            if (preg_match('/^UPDATE\s+(\w+)\s+SET\s+(.+?)\s+WHERE\s+(.+?)(?:\s+LIMIT\s+\d+)?$/is', $sql, $m)) {
                return $this->update($m[1], $this->assignments($m[2]), $m[3]);
            }

            return ['status' => 'success', 'data' => [], 'affected_rows' => 0];
        } catch (Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage(), 'data' => []];
        }
    }

    private function select(string $sql): array {
        $table = null;
        if (preg_match('/\sFROM\s+(\w+)/i', $sql, $m)) {
            $table = $m[1];
        }
        if (!$table || !isset($this->data[$table])) {
            return ['status' => 'success', 'data' => []];
        }

        $rows = $this->data[$table];
        if (preg_match("/category\s*=\s*'([^']+)'/i", $sql, $m)) {
            $rows = array_values(array_filter($rows, fn($row) => (string)($row['category'] ?? '') === $m[1]));
        }
        if (preg_match_all("/category\s*!=\s*'([^']+)'/i", $sql, $matches)) {
            foreach ($matches[1] as $excludedCategory) {
                $rows = array_values(array_filter($rows, fn($row) => (string)($row['category'] ?? '') !== $excludedCategory));
            }
        }

        $queryNeedles = $this->likeNeedles($sql);
        if ($queryNeedles) {
            $rows = array_values(array_filter($rows, function ($row) use ($queryNeedles) {
                $haystack = strtolower(implode(' ', array_map('strval', $row)));
                foreach ($queryNeedles as $needle) {
                    if ($needle !== '' && str_contains($haystack, strtolower($needle))) {
                        return true;
                    }
                }
                return false;
            }));
        }

        usort($rows, fn($a, $b) => ((int)($b['id'] ?? 0)) <=> ((int)($a['id'] ?? 0)));
        $limit = 50;
        if (preg_match('/\sLIMIT\s+(\d+)/i', $sql, $m)) {
            $limit = max(1, (int)$m[1]);
        }

        return ['status' => 'success', 'data' => array_slice($rows, 0, $limit)];
    }

    private function insert(string $table, array $columns, array $values): array {
        $row = [];
        foreach ($columns as $i => $column) {
            $row[$column] = $values[$i] ?? '';
        }
        $row['id'] = $this->nextId($table);
        $this->data[$table][] = $row;
        $this->save();
        return ['status' => 'success', 'data' => [], 'affected_rows' => 1, 'insert_id' => $row['id']];
    }

    private function update(string $table, array $assignments, string $where): array {
        if (!isset($this->data[$table])) {
            return ['status' => 'success', 'data' => [], 'affected_rows' => 0];
        }

        $affected = 0;
        foreach ($this->data[$table] as &$row) {
            if ($this->matchesWhere($row, $where)) {
                foreach ($assignments as $key => $value) {
                    $row[$key] = $value;
                }
                $affected++;
            }
        }
        unset($row);

        if ($affected > 0) {
            $this->save();
        }
        return ['status' => 'success', 'data' => [], 'affected_rows' => $affected];
    }

    private function matchesWhere(array $row, string $where): bool {
        if (preg_match_all("/(\w+)\s*=\s*'([^']*)'/", $where, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if ((string)($row[$match[1]] ?? '') !== $match[2]) {
                    return false;
                }
            }
        }
        return true;
    }

    private function columns(string $csv): array {
        return array_map(fn($col) => trim($col, " \t\n\r\0\x0B`"), explode(',', $csv));
    }

    private function values(string $csv): array {
        $values = str_getcsv($csv, ',', "'", "\\");
        return array_map(fn($value) => stripcslashes(trim($value)), $values);
    }

    private function assignments(string $csv): array {
        $result = [];
        foreach (str_getcsv($csv, ',', "'", "\\") as $part) {
            if (str_contains($part, '=')) {
                [$key, $value] = explode('=', $part, 2);
                $result[trim($key, " \t\n\r\0\x0B`")] = stripcslashes(trim($value, " \t\n\r\0\x0B'"));
            }
        }
        return $result;
    }

    private function likeNeedles(string $sql): array {
        preg_match_all("/LIKE\s+'%([^']+)%'/i", $sql, $matches);
        return array_values(array_unique($matches[1] ?? []));
    }

    private function nextId(string $table): int {
        $max = 0;
        foreach ($this->data[$table] ?? [] as $row) {
            $max = max($max, (int)($row['id'] ?? 0));
        }
        return $max + 1;
    }

    private function load(): void {
        if (!is_file($this->path)) {
            return;
        }

        $loaded = json_decode((string)file_get_contents($this->path), true);
        if (is_array($loaded)) {
            $this->data = array_replace($this->data, $loaded);
        }
    }

    private function save(): void {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($this->path, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

class HritikRemoteDB {
    private string $url;
    private string $apiKey;
    private HritikLocalDB $fallback;
    private int $timeout;
    private bool $verifySsl;
    private static bool $remoteBlocked = false;

    public function __construct(string $url, string $apiKey, ?HritikLocalDB $fallback = null, int $timeout = 15, bool $verifySsl = true) {
        $this->url = $url;
        $this->apiKey = $apiKey;
        $this->fallback = $fallback ?: new HritikLocalDB();
        $this->timeout = $timeout;
        $this->verifySsl = $verifySsl;
    }

    public function query(string $sql): array {
        $sql = trim($sql);
        if ($sql === '') {
            return ['status' => 'success', 'data' => []];
        }

        if (self::$remoteBlocked) {
            $local = $this->fallback->query($sql);
            $local['remote_error'] = 'Remote DB blocked by security challenge (cached)';
            return $local;
        }

        $remote = $this->queryRemote($sql);
        if (($remote['status'] ?? '') === 'success') {
            return $remote;
        }

        if (str_contains($remote['message'] ?? '', 'blocked by security challenge') || str_contains($remote['message'] ?? '', 'anti-bot')) {
            self::$remoteBlocked = true;
        }

        $local = $this->fallback->query($sql);
        $local['remote_error'] = $remote['message'] ?? 'Remote DB unavailable';
        if ($this->isWriteQuery($sql)) {
            $this->queueFailedWrite($sql, $local['remote_error']);
            $local['queued_for_remote_sync'] = true;
        }
        return $local;
    }

    private function queryRemote(string $sql): array {
        if (!function_exists('curl_init')) {
            return ['status' => 'error', 'message' => 'PHP cURL extension is not enabled'];
        }

        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['sql' => base64_encode($sql)]),
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $this->apiKey,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_COOKIE => '__test=cdb8b320d3a5a1f2e4c9f139; expires=Thu, 31-Dec-37 23:55:55 GMT; path=/', // Placeholder, user will update this
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => min(3, $this->timeout),
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
        ]);

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $body === '') {
            return ['status' => 'error', 'message' => $error ?: 'Empty remote DB response'];
        }

        if (str_contains($body, '<script') || str_contains($body, '/aes.js') || str_contains($body, 'epg_redirect')) {
            return ['status' => 'error', 'message' => 'Remote DB blocked by security challenge (anti-bot)', 'raw' => substr($body, 0, 300)];
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return ['status' => 'error', 'message' => "Invalid remote DB JSON (HTTP {$code})", 'raw' => substr($body, 0, 300)];
        }

        if ($code >= 400 && ($data['status'] ?? '') !== 'success') {
            $data['status'] = 'error';
        }

        return $data;
    }

    private function isWriteQuery(string $sql): bool {
        return (bool)preg_match('/^\s*(INSERT|REPLACE|UPDATE|DELETE|CREATE|ALTER|DROP)\b/i', $sql);
    }

    private function queueFailedWrite(string $sql, string $error): void {
        $path = __DIR__ . '/storage/remote_db_queue.json';
        $queue = [];
        if (is_file($path)) {
            $loaded = json_decode((string)file_get_contents($path), true);
            if (is_array($loaded)) {
                $queue = $loaded;
            }
        }

        $hash = hash('sha256', $sql);
        foreach ($queue as $item) {
            if (($item['hash'] ?? '') === $hash) {
                return;
            }
        }

        $queue[] = [
            'hash' => $hash,
            'sql' => $sql,
            'error' => $error,
            'queued_at' => date('c'),
        ];

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, json_encode($queue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

$remoteUrl = 'https://databasehritikai.techelevatex.us.cc/api.php';
$remoteKey = 'SACHIN_SECURE_V1_2026';
$verifySsl = getenv('HRITIK_REMOTE_DB_SSL_VERIFY') === '1' ? true : false; // Default false for local dev to avoid cert issues
$dbMode = strtolower((string)(getenv('HRITIK_DB_MODE') ?: 'remote'));

if (!isset($db)) {
    // Attempt Remote DB, but allow Local Fallback
    $fallback = new HritikLocalDB();
    $db = new HritikRemoteDB($remoteUrl, $remoteKey, $fallback, 5, $verifySsl);
}
