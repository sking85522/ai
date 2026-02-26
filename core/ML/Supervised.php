<?php
class Supervised
{
    private array $labeledPatterns = [
        'greeting' => ['hello', 'hi', 'hey', 'namaste'],
        'farewell' => ['bye', 'goodbye', 'later'],
        'time_query' => ['time', 'clock', 'date', 'today'],
        'help' => ['help', 'guide', 'support'],
        'normal_chat' => ['how', 'you', 'kaise', 'haal'],
        'code_generate' => ['code', 'php', 'html', 'sql', 'api', 'javascript', 'css'],
        'math_query' => ['plus', 'minus', 'square', 'math', 'sin', 'cos', 'tan', 'vector'],
        'table_request' => ['table', 'pahada', 'phada', 'multiplication'],
        'quiz_request' => ['quiz', 'mcq', 'test'],
        'task_mode' => ['task', 'complete', 'fully', 'execute', 'step'],
        'engineering_plan' => ['architecture', 'design', 'plan', 'system'],
        'debug_help' => ['debug', 'bug', 'error', 'fix'],
        'memory_store' => ['remember', 'name', 'save', 'yaad'],
        'memory_recall' => ['remember', 'yaad', 'my name', 'friend name', 'mera naam'],
    ];

    public function train(string $label, array $tokens): void
    {
        if (count($tokens) < 2) {
            return;
        }
        $this->labeledPatterns[$label] = array_values(array_unique(array_merge(
            $this->labeledPatterns[$label] ?? [],
            $tokens
        )));
    }

    public function predict(array $tokens): array
    {
        $tokenSet = array_flip($tokens);
        $bestLabel = 'unknown';
        $bestScore = 0.0;

        foreach ($this->labeledPatterns as $label => $patternTokens) {
            if ($patternTokens === []) {
                continue;
            }
            $matches = 0;
            foreach ($patternTokens as $patternToken) {
                if (isset($tokenSet[$patternToken])) {
                    $matches++;
                }
            }
            $score = $matches / max(1, count($patternTokens));
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestLabel = $label;
            }
        }

        return ['label' => $bestLabel, 'score' => round($bestScore, 4)];
    }
}
