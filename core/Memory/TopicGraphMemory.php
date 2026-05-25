<?php
namespace Core\Memory;

class TopicGraphMemory {
    private array $graph = [];

    /**
     * Connect two topics.
     */
    public function connect(string $topicA, string $topicB): void {
        $this->graph[$topicA][] = $topicB;
        $this->graph[$topicB][] = $topicA;
        $this->graph[$topicA] = array_unique($this->graph[$topicA]);
        $this->graph[$topicB] = array_unique($this->graph[$topicB]);
    }

    /**
     * Get related topics for a given term.
     */
    public function getRelated(string $topic): array {
        return $this->graph[$topic] ?? [];
    }

    private string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?: __DIR__ . '/../../storage/training/topic_graph.json';
        if (!is_file($this->file)) {
            file_put_contents($this->file, json_encode(['nodes' => [], 'edges' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    public function rememberTopics(array $tokens): void
    {
        $tokens = array_values(array_unique(array_filter($tokens, static fn($t) => strlen((string) $t) >= 4)));
        if (!$tokens) {
            return;
        }

        $data = $this->read();
        foreach ($tokens as $token) {
            $data['nodes'][$token] = (int) ($data['nodes'][$token] ?? 0) + 1;
        }

        $slice = array_slice($tokens, 0, 6);
        $count = count($slice);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $a = $slice[$i];
                $b = $slice[$j];
                $key = $a < $b ? $a . '|' . $b : $b . '|' . $a;
                $data['edges'][$key] = round(((float) ($data['edges'][$key] ?? 0.0)) + 0.1, 4);
            }
        }

        $this->write($data);
    }

    public function topTopics(int $limit = 8): array
    {
        $data = $this->read();
        $nodes = $data['nodes'] ?? [];
        arsort($nodes);
        return array_slice(array_keys($nodes), 0, $limit);
    }

    public function stats(): array
    {
        $data = $this->read();
        return [
            'nodes' => count($data['nodes'] ?? []),
            'edges' => count($data['edges'] ?? []),
        ];
    }

    private function read(): array
    {
        $raw = file_get_contents($this->file);
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            return ['nodes' => [], 'edges' => []];
        }
        $data['nodes'] = is_array($data['nodes'] ?? null) ? $data['nodes'] : [];
        $data['edges'] = is_array($data['edges'] ?? null) ? $data['edges'] : [];
        return $data;
    }

    private function write(array $data): void
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
