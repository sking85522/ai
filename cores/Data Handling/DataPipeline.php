<?php
class DataPipeline
{
    public function buildDataset(array $samples): array
    {
        $rows = [];
        foreach ($samples as $sample) {
            if (!is_array($sample)) {
                continue;
            }
            $text = trim((string) ($sample['text'] ?? ''));
            $label = trim((string) ($sample['label'] ?? 'unknown'));
            if ($text === '' || $label === '') {
                continue;
            }
            $clean = $this->normalize($text);
            $rows[] = [
                'text' => $clean,
                'label' => $label,
                'tokens' => $this->tokens($clean),
            ];
        }
        return $rows;
    }

    public function splitTrainTest(array $rows, float $trainRatio = 0.8): array
    {
        $rows = array_values($rows);
        shuffle($rows);
        $cut = (int) floor(count($rows) * $trainRatio);
        $cut = max(1, min($cut, max(1, count($rows) - 1)));
        return [
            'train' => array_slice($rows, 0, $cut),
            'test' => array_slice($rows, $cut),
        ];
    }

    private function normalize(string $text): string
    {
        $x = mb_strtolower($text, 'UTF-8');
        $x = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $x) ?? $x;
        $x = preg_replace('/\s+/', ' ', $x) ?? $x;
        return trim($x);
    }

    private function tokens(string $text): array
    {
        $parts = array_filter(explode(' ', $text), static fn($t) => strlen((string) $t) >= 2);
        return array_values($parts);
    }
}
