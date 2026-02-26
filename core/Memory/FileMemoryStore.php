<?php
class FileMemoryStore
{
    private string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?: __DIR__ . '/../../storage/training/memory_store.json';
        if (!is_file($this->file)) {
            file_put_contents($this->file, json_encode(['facts' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
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
