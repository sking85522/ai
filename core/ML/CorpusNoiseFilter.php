<?php
namespace Core\ML;

class CorpusNoiseFilter {
    /**
     * Filters noise from training data.
     * Heuristics: min length, repetitive chars, gibberish detection.
     */
    public function isGoodQA(string $q, string $a): bool {
        if (strlen($q) < 5 || strlen($a) < 2) return false;
        
        // Check for too many repetitive characters (e.g., "aaaaaaaa")
        if (preg_match('/(.)\1{4,}/', $q)) return false;

        return true;
    }

    public function clean(string $text): string {
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    public function normalize(string $text): string
    {
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';
        return trim($text);
    }

    public function isGoodTrainingText(string $text): bool
    {
        $text = $this->normalize($text);
        if ($text === '') {
            return false;
        }
        $len = mb_strlen($text, 'UTF-8');
        if ($len < 8 || $len > 1200) {
            return false;
        }
        if ($this->looksLikeNoise($text)) {
            return false;
        }
        if ($this->tokenCount($text) < 2) {
            return false;
        }
        return true;
    }

    public function isGoodQA(string $question, string $answer): bool
    {
        $q = $this->normalize($question);
        $a = $this->normalize($answer);
        if (!$this->isGoodTrainingText($q) || !$this->isGoodTrainingText($a)) {
            return false;
        }
        if (mb_strlen($a, 'UTF-8') < 6) {
            return false;
        }
        if (mb_strlen($a, 'UTF-8') > 1800) {
            return false;
        }
        if ($this->tokenCount($a) < 2) {
            return false;
        }
        return true;
    }

    private function looksLikeNoise(string $text): bool
    {
        $lower = mb_strtolower($text, 'UTF-8');

        $badFragments = [
            'http://', 'https://', 'www.', 'ftp://',
            'archive-name:', 'from:', 'subject:',
            'copyright', 'license', 'all rights reserved',
            '####', '====', '----', '<<<<', '>>>>',
            '.com/', '.org/',
        ];
        foreach ($badFragments as $frag) {
            if (str_contains($lower, $frag) && mb_strlen($text, 'UTF-8') < 120) {
                return true;
            }
        }

        if (preg_match('/^[\W_]+$/u', $text) === 1) {
            return true;
        }

        $alpha = preg_match_all('/[\p{L}]/u', $text) ?: 0;
        $digit = preg_match_all('/[\p{N}]/u', $text) ?: 0;
        $symbol = preg_match_all('/[^\p{L}\p{N}\s]/u', $text) ?: 0;
        $total = max(1, mb_strlen($text, 'UTF-8'));

        if (($alpha / $total) < 0.25) {
            return true;
        }
        if (($symbol / $total) > 0.35) {
            return true;
        }
        if (($digit / $total) > 0.45 && $alpha < 5) {
            return true;
        }

        // Reject repetitive tokens like "aaaa aaaa aaaa"
        $tokens = preg_split('/\s+/u', $lower) ?: [];
        $tokens = array_values(array_filter($tokens, static fn($t) => $t !== ''));
        if (count($tokens) >= 4) {
            $unique = count(array_unique($tokens));
            if ($unique <= 2) {
                return true;
            }
        }

        return false;
    }

    private function tokenCount(string $text): int
    {
        $tokens = preg_split('/\s+/u', $text) ?: [];
        $tokens = array_values(array_filter($tokens, static fn($t) => $t !== ''));
        return count($tokens);
    }
}
class CorpusShardManager {
    /**
     * Splits a large corpus into smaller shards for memory efficiency.
     */
    public function shard(array $data, int $size = 100): array {
        return array_chunk($data, $size);
    }
}