<?php
class SimpleTextClassifier
{
    private array $labelTokenCounts = [];
    private array $labelCounts = [];
    private array $vocab = [];

    public function train(array $trainRows): void
    {
        foreach ($trainRows as $row) {
            $label = (string) ($row['label'] ?? 'unknown');
            $tokens = (array) ($row['tokens'] ?? []);
            $this->labelCounts[$label] = (int) ($this->labelCounts[$label] ?? 0) + 1;
            foreach ($tokens as $token) {
                $token = (string) $token;
                if ($token === '') {
                    continue;
                }
                $this->vocab[$token] = true;
                $this->labelTokenCounts[$label][$token] = (int) (($this->labelTokenCounts[$label][$token] ?? 0) + 1);
            }
        }
    }

    public function predict(array $tokens): string
    {
        if (!$this->labelCounts) {
            return 'unknown';
        }
        $vocabSize = max(1, count($this->vocab));
        $totalDocs = array_sum($this->labelCounts);
        $bestLabel = 'unknown';
        $bestScore = -INF;

        foreach ($this->labelCounts as $label => $count) {
            $prior = log($count / max(1, $totalDocs));
            $tokenTotal = array_sum($this->labelTokenCounts[$label] ?? []);
            $score = $prior;
            foreach ($tokens as $token) {
                $freq = (int) (($this->labelTokenCounts[$label][$token] ?? 0));
                $score += log(($freq + 1) / ($tokenTotal + $vocabSize));
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestLabel = (string) $label;
            }
        }
        return $bestLabel;
    }
}
