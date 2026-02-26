<?php
class MemoryRanker
{
    public function pickBest(string $query, array $candidates): ?array
    {
        $query = mb_strtolower(trim($query), 'UTF-8');
        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }
            $key = mb_strtolower((string) ($candidate['key'] ?? ''), 'UTF-8');
            $value = (string) ($candidate['value'] ?? '');
            if ($key === '' || $value === '') {
                continue;
            }
            similar_text($query, $key, $scoreKey);
            similar_text($query, mb_strtolower($value, 'UTF-8'), $scoreVal);
            $score = ($scoreKey * 0.75) + ($scoreVal * 0.25);
            if (str_contains($query, $key)) {
                $score += 30.0;
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $candidate;
            }
        }

        if ($bestScore < 45.0) {
            return null;
        }
        $best['rank_score'] = round($bestScore / 100, 4);
        return $best;
    }
}
