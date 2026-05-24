<?php
namespace Core\Training\LanguageModel;

class CorpusBuilder {
    public function buildFromPairs(array $pairs): array {
        $documents = [];
        foreach ($pairs as $pair) {
            $prompt = trim((string)($pair['prompt'] ?? $pair['question'] ?? ''));
            $response = $this->extractResponseText($pair);
            if ($prompt === '' || $response === '') {
                continue;
            }

            $documents[] = $this->normalize("Q: {$prompt}\nA: {$response}");
        }

        return array_values(array_filter($documents));
    }

    public function buildFromJsonFile(string $path): array {
        if (!is_file($path)) {
            return [];
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            return [];
        }

        return $this->buildFromPairs($data);
    }

    private function extractResponseText(array $pair): string {
        $response = trim((string)($pair['response'] ?? $pair['answer'] ?? ''));
        if ($response === '') {
            return '';
        }

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            foreach (['answer', 'content', 'response', 'summary', 'label'] as $field) {
                if (!empty($decoded[$field]) && is_string($decoded[$field])) {
                    return trim($decoded[$field]);
                }
            }
        }

        return $response;
    }

    private function normalize(string $text): string {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
}
