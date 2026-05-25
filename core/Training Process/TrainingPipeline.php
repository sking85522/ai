<?php
class TrainingPipeline
{
    public function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $tokens ?: [];
    }

    public function buildDataset(array $items): array
    {
        $rows = [];
        foreach ($items as $it) {
            $text = $it['text'] ?? '';
            $tokens = $this->tokenize($text);
            $rows[] = [
                'text' => $text,
                'tokens' => $tokens,
                'label' => $it['label'] ?? null,
            ];
        }
        return $rows;
    }

    public function splitTrainTest(array $rows, float $trainFraction = 0.8): array
    {
        if ($rows === []) {
            return ['train' => [], 'test' => []];
        }
        shuffle($rows);
        $n = (int) max(1, floor(count($rows) * $trainFraction));
        return [
            'train' => array_slice($rows, 0, $n),
            'test' => array_slice($rows, $n),
        ];
    }
}
