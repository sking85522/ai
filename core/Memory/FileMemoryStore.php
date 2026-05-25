<?php
namespace Core\Memory;

/**
 * HRITIK AI - FILE MEMORY STORE
 * Handles persistent storage for chats, profiles, and neural context.
 */
class FileMemoryStore {
    private string $storagePath;
    private bool $useRemoteDb = true;
    private string $file;

    public function __construct(?string $storageDir = null) {
        $file = $storageDir;

        if (!$storageDir) {
            $this->storagePath = dirname(__DIR__, 2) . '/localstorage/data';
        } else {
            $this->storagePath = rtrim($storageDir, '/');
        }

        global $db;
        $this->useRemoteDb = isset($db) && $db !== null;
        if (!$this->useRemoteDb && !is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    
        $this->file = $file ?: __DIR__ . '/../../storage/training/memory_store.json';
        if (!is_file($this->file)) {
            file_put_contents($this->file, json_encode(['facts' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    private function getNamespacePath(string $namespace): string {
        $path = $this->storagePath . '/' . $namespace;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    private function sanitizeId(string $id): string {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $id);
    }

    public function set(string $id, array $data, string $namespace = 'chats'): bool {
        if ($this->useRemoteDb) {
            global $db;
            $safeId = addslashes($this->sanitizeId($id));
            $safeNamespace = addslashes($namespace);
            $safeData = addslashes(json_encode($data, JSON_UNESCAPED_SLASHES));
            $sql = "REPLACE INTO neural_file_memory (mem_namespace, mem_id, mem_json) VALUES ('{$safeNamespace}', '{$safeId}', '{$safeData}')";
            $res = $db->query($sql);
            if (($res['status'] ?? '') === 'error') {
                $sql = "REPLACE INTO neural_memory (category, m_key, m_value) VALUES ('file_memory:{$safeNamespace}', '{$safeId}', '{$safeData}')";
                $res = $db->query($sql);
            }
            return isset($res['status']) && $res['status'] === 'success';
        }

        $path = $this->getNamespacePath($namespace);
        $file = $path . '/' . $this->sanitizeId($id) . '.json';
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    public function get(string $id, string $namespace = 'chats'): array {
        if ($this->useRemoteDb) {
            global $db;
            $safeId = addslashes($this->sanitizeId($id));
            $safeNamespace = addslashes($namespace);
            $sql = "SELECT mem_json FROM neural_file_memory WHERE mem_namespace='{$safeNamespace}' AND mem_id='{$safeId}' LIMIT 1";
            $res = $db->query($sql);
            if (($res['status'] ?? '') === 'error') {
                $sql = "SELECT m_value AS mem_json FROM neural_memory WHERE category='file_memory:{$safeNamespace}' AND m_key='{$safeId}' LIMIT 1";
                $res = $db->query($sql);
            }
            $json = $res['data'][0]['mem_json'] ?? '';
            $decoded = json_decode((string)$json, true);
            return is_array($decoded) ? $decoded : [];
        }

        $path = $this->getNamespacePath($namespace);
        $file = $path . '/' . $this->sanitizeId($id) . '.json';
        if (file_exists($file)) {
            $content = file_get_contents($file);
            return json_decode($content, true) ?? [];
        }
        return [];
    }

    public function append(string $id, $data, string $namespace = 'chats'): bool {
        $memory = $this->get($id, $namespace);
        $memory[] = $data;
        return $this->set($id, $memory, $namespace);
    }

    public function getAllNamespace(string $namespace = 'chats'): array {
        if ($this->useRemoteDb) {
            global $db;
            $safeNamespace = addslashes($namespace);
            $sql = "SELECT mem_id, mem_json FROM neural_file_memory WHERE mem_namespace='{$safeNamespace}'";
            $res = $db->query($sql);
            if (($res['status'] ?? '') === 'error') {
                $sql = "SELECT m_key AS mem_id, m_value AS mem_json FROM neural_memory WHERE category='file_memory:{$safeNamespace}'";
                $res = $db->query($sql);
            }

            $all = [];
            foreach (($res['data'] ?? []) as $row) {
                $decoded = json_decode((string)($row['mem_json'] ?? ''), true);
                $all[(string)($row['mem_id'] ?? '')] = is_array($decoded) ? $decoded : [];
            }
            return $all;
        }

        $path = $this->getNamespacePath($namespace);
        $all = [];

        foreach (glob($path . '/*.json') ?: [] as $file) {
            $id = basename($file, '.json');
            $content = file_get_contents($file);
            $all[$id] = json_decode((string)$content, true) ?? [];
        }

        return $all;
    }


  

    public function remember(string $key, string $value, string $language = 'bilingual'): void
    {
        $data = $this->read();
        $data['facts'][$this->normalize($key)] = [
            'key' => $key,
            'value' => $value,
            'language' => $language,
            'updated_at' => date('c'),
        ];
        $this->write($data);
    }

    public function recall(string $query): ?array
    {
        $facts = $this->read()['facts'] ?? [];
        if (!$facts) {
            return null;
        }

        $queryNorm = $this->normalize($query);
        $directKey = $this->detectFactKey($queryNorm);
        if ($directKey !== null && isset($facts[$directKey])) {
            return $facts[$directKey];
        }

        $best = null;
        $bestScore = 0.0;
        foreach ($facts as $fact) {
            $candidate = 'user_fact::' . $this->normalize((string) ($fact['key'] ?? ''));
            similar_text($queryNorm, $candidate, $score);
            $score = (float) $score;
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $fact;
            }
        }

        return $bestScore >= 45.0 ? $best : null;
    }

    private function read(): array
    {
        $raw = file_get_contents($this->file);
        $data = json_decode((string) $raw, true);
        return is_array($data) ? $data : ['facts' => []];
    }

    private function write(array $data): void
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        return preg_replace('/\s+/', ' ', $text) ?? '';
    }

    private function detectFactKey(string $query): ?string
    {
        if (str_contains($query, 'my name') || str_contains($query, 'mera naam')) {
            return 'name';
        }
        if (str_contains($query, 'my city') || str_contains($query, 'i live') || str_contains($query, 'kahan')) {
            return 'city';
        }
        if (str_contains($query, 'friend name') || str_contains($query, 'mere friend ka naam')) {
            return 'friend_name';
        }
        if (str_contains($query, 'my phone') || str_contains($query, 'mera number')) {
            return 'phone';
        }
        return null;
    }
}
