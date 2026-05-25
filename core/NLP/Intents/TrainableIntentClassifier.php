<?php
namespace Core\NLP\Intents;

class TrainableIntentClassifier {
    private array $labels = [];
    private array $labelCounts = [];
    private array $tokenCounts = [];
    private array $labelTokenTotals = [];
    private array $vocabulary = [];
    private int $totalDocs = 0;
    private float $threshold;

    public function __construct(float $threshold = 0.42) {
        $this->threshold = $threshold;
    }

    public static function load(?string $path = null): ?self {
        $path = $path ?: dirname(__DIR__, 3) . '/storage/models/intent_classifier.json';
        if (!is_file($path)) {
            return null;
        }

        $data = json_decode((string)file_get_contents($path), true);
        if (!is_array($data) || empty($data['labels'])) {
            return null;
        }

        $model = new self((float)($data['threshold'] ?? 0.42));
        $model->labels = $data['labels'] ?? [];
        $model->labelCounts = $data['label_counts'] ?? [];
        $model->tokenCounts = $data['token_counts'] ?? [];
        $model->labelTokenTotals = $data['label_token_totals'] ?? [];
        $model->vocabulary = $data['vocabulary'] ?? [];
        $model->totalDocs = (int)($data['total_docs'] ?? 0);
        return $model;
    }

    public function train(string $text, string $label): void {
        $label = $this->normalizeLabel($label);
        if ($text === '' || $label === '') {
            return;
        }

        if (!isset($this->labelCounts[$label])) {
            $this->labels[] = $label;
            $this->labelCounts[$label] = 0;
            $this->tokenCounts[$label] = [];
            $this->labelTokenTotals[$label] = 0;
        }

        $this->labelCounts[$label]++;
        $this->totalDocs++;

        foreach ($this->tokens($text) as $token) {
            $this->vocabulary[$token] = true;
            $this->tokenCounts[$label][$token] = ($this->tokenCounts[$label][$token] ?? 0) + 1;
            $this->labelTokenTotals[$label]++;
        }
    }

    public function predict(string $text): array {
        if ($this->totalDocs === 0 || empty($this->labels)) {
            return ['intent' => 'unknown', 'confidence' => 0.0, 'scores' => []];
        }

        $tokens = $this->tokens($text);
        if (empty($tokens)) {
            return ['intent' => 'unknown', 'confidence' => 0.0, 'scores' => []];
        }

        $scores = [];
        $vocabSize = max(1, count($this->vocabulary));
        foreach ($this->labels as $label) {
            $logProb = log(($this->labelCounts[$label] ?? 1) / max(1, $this->totalDocs));
            $tokenTotal = max(0, (int)($this->labelTokenTotals[$label] ?? 0));
            foreach ($tokens as $token) {
                $count = $this->tokenCounts[$label][$token] ?? 0;
                $logProb += log(($count + 1) / ($tokenTotal + $vocabSize));
            }
            $scores[$label] = $logProb;
        }

        arsort($scores);
        $best = (string)array_key_first($scores);
        $confidence = $this->confidence($scores);

        return [
            'intent' => $confidence >= $this->threshold ? $best : 'unknown',
            'raw_intent' => $best,
            'confidence' => $confidence,
            'scores' => $scores
        ];
    }

    public function save(?string $path = null): bool {
        $path = $path ?: dirname(__DIR__, 3) . '/storage/models/intent_classifier.json';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return (bool)file_put_contents($path, json_encode([
            'version' => 1,
            'threshold' => $this->threshold,
            'labels' => $this->labels,
            'label_counts' => $this->labelCounts,
            'token_counts' => $this->tokenCounts,
            'label_token_totals' => $this->labelTokenTotals,
            'vocabulary' => $this->vocabulary,
            'total_docs' => $this->totalDocs,
            'saved_at' => date('c')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function confidence(array $scores): float {
        if (empty($scores)) {
            return 0.0;
        }

        $max = max($scores);
        $exp = [];
        foreach ($scores as $label => $score) {
            $exp[$label] = exp($score - $max);
        }
        $sum = array_sum($exp);
        if ($sum <= 0) {
            return 0.0;
        }

        $first = reset($exp);
        return min(1.0, max(0.0, $first / $sum));
    }

    private function tokens(string $text): array {
        $text = strtolower(trim($text));
        $text = strtr($text, [
            'kese' => 'kaise',
            'kon' => 'kaun',
            'btao' => 'batao',
            'bnao' => 'banao',
            'krdo' => 'karo',
            'h' => 'hai',
        ]);
        $text = preg_replace('/[^a-z0-9+#. ]+/i', ' ', (string)$text);
        $parts = preg_split('/\s+/', trim((string)$text)) ?: [];
        $stop = array_flip(['hai', 'ho', 'ka', 'ki', 'ke', 'ko', 'me', 'mein', 'the', 'is', 'are', 'a', 'an']);
        $tokens = [];
        foreach ($parts as $part) {
            if ($part === '' || strlen($part) < 2 || isset($stop[$part])) {
                continue;
            }
            $tokens[] = $part;
        }
        return $tokens;
    }

    private function normalizeLabel(string $label): string {
        $label = strtolower(trim($label));
        $label = preg_replace('/[^a-z0-9_]+/', '_', (string)$label);
        return trim((string)$label, '_');
    }
}
