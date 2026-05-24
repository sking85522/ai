<?php
namespace Core\Response;

/**
 * Shared quality gate for filtering junk, decoding structured payloads,
 * and scoring candidate responses across modules.
 */
class ResponseQualityGuard {
    private array $blockedFragments = [
        'main is baare mein thoda aur seekh raha hoon',
        'kya aap mujhe iska sahi jawab bata sakte hain',
        'not learned enough',
        'fact check:',
        'contradiction',
        'entailment',
        'neutral\t(',
    ];

    public function clean(string $text): string {
        $text = trim(str_replace('__###newline###__', "\n", $text));
        if ($text === '') {
            return '';
        }

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            foreach (['answer', 'content', 'response', 'summary', 'label'] as $field) {
                if (!empty($decoded[$field]) && is_string($decoded[$field])) {
                    $text = $decoded[$field];
                    break;
                }
            }
        }

        $text = preg_replace('/\s+/u', ' ', trim($text));
        return trim($text);
    }

    /**
     * Removes repetitive words and phrases from the text.
     */
    public function removeRepetition(string $text): string {
        // Remove immediate word repetitions
        $words = explode(' ', $text);
        $result = [];
        $lastWord = '';
        foreach ($words as $word) {
            $w = strtolower(trim($word, " ,.!?"));
            if ($w === '' || $w === $lastWord) continue;
            $result[] = $word;
            $lastWord = $w;
        }
        
        $text = implode(' ', $result);
        
        // Remove repetitive phrases (e.g., "kya haal hai kya haal hai")
        $text = preg_replace('/(.+?)(?:\s+\1){1,}/i', '$1', $text);
        
        return trim($text);
    }

    public function isJunk(?string $text): bool {
        if ($text === null) {
            return true;
        }

        $text = $this->clean($text);
        $normalized = strtolower($text);

        static $allowed = ['qa'=>true, 'general'=>true, 'php'=>true];
        if ($normalized === '' || isset($allowed[$normalized])) {
            return true;
        }

        foreach ($this->blockedFragments as $fragment) {
            if (str_contains($normalized, $fragment)) {
                return true;
            }
        }

        if (preg_match('/\(\s*\(\s*\(/', $text)) {
            return true;
        }

        if (substr_count($text, '{') > 8 || substr_count($text, '[') > 8) {
            return true;
        }

        if (preg_match('/\d+ [\+\-\*\/] \d+ = \d+/u', $text)) {
            return true;
        }

        return false;
    }

    public function score(string $prompt, ?string $candidate, string $source = 'unknown', array $tokens = []): int {
        if ($this->isJunk($candidate)) {
            return -100;
        }

        $candidate = $this->clean((string)$candidate);
        $candidateLower = strtolower($candidate);
        $promptLower = strtolower($prompt);
        $tokens = $tokens ?: $this->extractKeywords($prompt);

        $score = $this->sourceWeight($source);
        $length = strlen($candidate);

        if ($length >= 40) $score += 6;
        if ($length >= 90) $score += 5;
        if ($length > 1800) $score -= 6;

        foreach ($tokens as $token) {
            if (str_contains($candidateLower, $token)) {
                $score += 8;
            }
        }

        if (preg_match('/^(what is|who is|php|java|python|[a-z0-9 _-]+ is )/i', $candidate)) {
            $score += 4;
        }

        if (preg_match('/(what is|kya h|kya hai|meaning|define)/i', $promptLower) && preg_match('/\b(is|means|ek|hai)\b/i', $candidate)) {
            $score += 6;
        }

        if (str_contains($candidateLower, strtolower(substr($prompt, 0, min(18, strlen($prompt)))))) {
            $score += 3;
        }

        return $score;
    }

    public function shortlist(array $candidates, string $prompt, array $tokens = []): array {
        $scored = [];
        foreach ($candidates as $candidate) {
            $text = $this->clean((string)($candidate['response'] ?? ''));
            $score = $this->score($prompt, $text, (string)($candidate['source'] ?? 'unknown'), $tokens);
            if ($score < 0) {
                continue;
            }

            $candidate['response'] = $text;
            $candidate['score'] = $score;
            $scored[] = $candidate;
        }

        usort($scored, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));
        return $scored;
    }

    public function extractKeywords(string $text): array {
        static $stopWords = ['is'=>true, 'the'=>true, 'a'=>true, 'an'=>true, 'me'=>true, 'my'=>true, 'what'=>true, 'who'=>true, 'how'=>true, 'where'=>true, 'kyu'=>true, 'kaise'=>true, 'btao'=>true, 'batano'=>true, 'kya'=>true, 'hai'=>true, 'ki'=>true, 'ka'=>true, 'ke'=>true, 'aur'=>true, 'mein'=>true, 'main'=>true, 'to'=>true, 'of'=>true, 'hritik'=>true, 'ai'=>true];
        $text = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text));
        $words = preg_split('/\s+/', trim($text));

        return array_values(array_filter($words ?: [], function ($word) use ($stopWords) {
            return strlen($word) > 2 && !isset($stopWords[$word]);
        }));
    }

    private function sourceWeight(string $source): int {
        return match ($source) {
            'semantic_search' => 22,
            'supervised_fact' => 20,
            'online_cloud_memory' => 18,
            'knowledge_memory' => 18,
            'module_integrator' => 16,
            'neural_reasoning' => 10,
            'local_language_model' => 12,
            'generative_ai' => 6,
            default => 8,
        };
    }
}
