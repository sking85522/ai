<?php
class ResponseRanker
{
    public function pickBestIntent(string $input, string $predictedIntent, float $predictedConfidence, array $intentCandidates): string
    {
        $scores = [];
        $scores[$predictedIntent] = ($scores[$predictedIntent] ?? 0.0) + max(0.0, min(1.0, $predictedConfidence));

        $pos = 0;
        foreach ($intentCandidates as $candidate) {
            $intent = (string) ($candidate['intent'] ?? 'general');
            $base = (float) ($candidate['score'] ?? 0.0);
            $posBoost = max(0.0, 0.25 - ($pos * 0.05));
            $scores[$intent] = ($scores[$intent] ?? 0.0) + $base + $posBoost;
            $pos++;
        }

        $inputLow = mb_strtolower($input, 'UTF-8');
        foreach ($scores as $intent => $score) {
            $scores[$intent] = $score + $this->intentKeywordBoost($intent, $inputLow);
        }

        // If question appears non-code, suppress accidental code routing.
        if (str_contains($inputLow, '?') || str_starts_with($inputLow, 'what is')) {
            $hasCodeSignal = preg_match('/\b(code|php|html|css|javascript|sql|api|function|debug|bug)\b/u', $inputLow) === 1;
            if (!$hasCodeSignal && isset($scores['code_generate'])) {
                $scores['code_generate'] -= 0.6;
            }
        }

        arsort($scores);
        $best = array_key_first($scores);
        return $best ?: 'general';
    }

    private function intentKeywordBoost(string $intent, string $input): float
    {
        $keywords = [
            'quiz_request' => ['quiz', 'mcq', 'प्रश्नोत्तरी', 'क्विज'],
            'table_request' => ['table', 'pahada', 'phada', 'multiplication table', 'पहाड़ा'],
            'math_query' => ['sin', 'cos', 'tan', 'vector', 'integral', 'derivative', 'avkalan', 'samakalan'],
            'code_generate' => ['code', 'php', 'html', 'css', 'javascript', 'api', 'sql'],
            'debug_help' => ['debug', 'bug', 'error', 'fix'],
            'engineering_plan' => ['architecture', 'plan', 'system design'],
            'memory_store' => ['remember that', 'save this', 'my name is', 'mera naam'],
            'memory_recall' => ['what is my', 'do you remember', 'yaad hai', 'my name'],
        ];

        $boost = 0.0;
        foreach (($keywords[$intent] ?? []) as $kw) {
            if ($this->containsWord($input, $kw)) {
                $boost += 0.15;
            }
        }
        return min(0.45, $boost);
    }

    private function containsWord(string $text, string $word): bool
    {
        $pattern = '/(^|[^\p{L}\p{N}_])' . preg_quote($word, '/') . '([^\p{L}\p{N}_]|$)/u';
        return preg_match($pattern, $text) === 1;
    }
}
