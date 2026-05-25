<?php
namespace Core\Evaluation;

class ConfidenceScorer {
    public function score(string $prompt, ?string $response, string $source = 'unknown', array $analysis = [], array $evidence = []): float {
        $response = trim((string)$response);
        if ($response === '') {
            return 0.0;
        }

        $sourceBase = [
            'pattern' => 0.82,
            'memory' => 0.72,
            'tool' => 0.86,
            'agentic' => 0.82,
            'module' => 0.78,
            'translation' => 0.72,
            'math' => 0.93,
            'rag' => 0.78,
            'knowledge' => 0.62,
            'database' => 0.5,
            'fallback' => 0.15,
            'local_teacher' => 0.76
        ];

        $score = $sourceBase[$source] ?? 0.45;

        if ($this->isWeak($response)) {
            $score = min($score, 0.18);
        }

        $len = strlen($response);
        if ($len > 40 && $len < 1600) {
            $score += 0.08;
        } elseif ($len < 16) {
            $score -= 0.12;
        }

        if (!empty($evidence)) {
            $bestEvidence = (float)($evidence[0]['score'] ?? 0.0);
            $score = max($score, min(0.93, 0.45 + ($bestEvidence * 0.48)));
        }

        return round(max(0.0, min(0.98, $score)), 3);
    }

    public function isWeak(?string $response): bool {
        $r = strtolower(trim((string)$response));
        if ($r === '') {
            return true;
        }

        $weak = [
            'study karni', 'exact jawab nahi', 'data bank', 'smarter ho raha', 'time dijiye',
            'training le raha', 'mujhe iske baare mein aur', 'i do not know', 'not sure'
        ];

        foreach ($weak as $phrase) {
            if (str_contains($r, $phrase)) {
                return true;
            }
        }

        return false;
    }
}
