<?php
namespace Core\NLP;

class InputNormalizer {
    /**
     * Normalizes text to a standard Unicode form (NFKC) and performs case folding.
     */
    public function normalize(string $text): string {
        // 1. Unicode Normalization (NFKC is good for web and mixing symbols)
        if (extension_loaded('intl')) {
            $text = \Normalizer::normalize($text, \Normalizer::FORM_KC);
        }

        // 2. Case folding (More robust than strtolower for some scripts)
        $text = mb_convert_case($text, MB_CASE_LOWER, 'UTF-8');

        // 3. Remove redundant whitespace
        $text = preg_replace('/\s+/u', ' ', $text);
        
        return trim($text);
    }

    /**
     * Specifically handles numeric and decimal normalization for Math tasks.
     */
    public function normalizeNumbers(string $text): string {
        // Ensure decimals like "5. 5" become "5.5"
        return preg_replace('/(\d+)\.\s+(\d+)/u', '$1.$2', $text);
    }

    /** @var array<string, true> */
    private array $fuzzyLexicon = [];

    public function normalize(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        // Normalize common Hinglish spellings for stronger intent matching.
        $map = [
            'krrha' => 'kar raha',
            'kr rha' => 'kar raha',
            'krna' => 'karna',
            'bnao' => 'banao',
            'bnado' => 'bana do',
            'samakl' => 'samakalan',
            'avkaln' => 'avkalan',
            'trignometry' => 'trigonometry',
            'tegnometry' => 'trigonometry',
            'dicrnory' => 'dictionary',
            'responce' => 'response',
            'nerol' => 'neural',
            'schemea' => 'schema',
            'tumara' => 'tumhara',
            'tmhara' => 'tumhara',
            'cpital' => 'capital',
            'capitel' => 'capital',
            'pakisatan' => 'pakistan',
            'pakstan' => 'pakistan',
            'bhart' => 'bharat',
            'utter pradesh' => 'uttar pradesh',
            'uttarpradesh' => 'uttar pradesh',
            'rajdhani' => 'capital',
            'kreej' => 'quiz',
            'kreez' => 'quiz',
            'क्रीज' => 'क्विज',
        ];

        $x = mb_strtolower($text, 'UTF-8');
        foreach ($map as $from => $to) {
            $x = str_replace($from, $to, $x);
        }

        $x = preg_replace('/[^\p{L}\p{N}\s\.\-\?]/u', ' ', $x) ?? $x;
        $x = preg_replace('/\s+/u', ' ', $x) ?? $x;
        $x = trim($x);

        $tokens = preg_split('/\s+/u', $x) ?: [];
        foreach ($tokens as $i => $token) {
            $tokens[$i] = $this->fuzzyCorrectToken($token);
        }
        $x = implode(' ', $tokens);

        // Canonicalize common mixed order prompts.
        $x = preg_replace('/\bcapital\s+([a-z][a-z\.\- ]{1,50})$/u', 'capital of $1', $x) ?? $x;
        $x = preg_replace('/\btabel\b/u', 'table', $x) ?? $x;

        return trim(preg_replace('/\s+/u', ' ', $x) ?? $x);
    }

    private function fuzzyCorrectToken(string $token): string
    {
        if (!preg_match('/^[a-z]{4,18}$/u', $token)) {
            return $token;
        }

        if (!$this->fuzzyLexicon) {
            $this->fuzzyLexicon = $this->buildLexicon();
        }

        if (isset($this->fuzzyLexicon[$token])) {
            return $token;
        }

        $best = $token;
        $bestDistance = PHP_INT_MAX;
        $first = $token[0] ?? '';

        foreach ($this->fuzzyLexicon as $candidate => $_) {
            if (($candidate[0] ?? '') !== $first) {
                continue;
            }
            $distance = levenshtein($token, $candidate);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $candidate;
                if ($distance === 1) {
                    break;
                }
            }
        }

        $maxDistance = strlen($token) <= 6 ? 1 : 2;
        return $bestDistance <= $maxDistance ? $best : $token;
    }

    /** @return array<string, true> */
    private function buildLexicon(): array
    {
        $vocabPath = dirname(dirname(__DIR__)) . '/storage/training/vocabulary.json';
        if (is_readable($vocabPath)) {
            $json = file_get_contents($vocabPath);
            $words = json_decode($json, true);
            if (is_array($words)) {
                return array_fill_keys($words, true);
            }
        }

        // Fallback to a minimal hardcoded list if file doesn't exist or is invalid
        $fallbackWords = [
            'capital', 'india', 'bharat', 'pakistan', 'japan', 'nepal', 'bangladesh', 'china',
            'usa', 'table', 'quiz', 'physics', 'chemistry', 'mathematics', 'math', 'trigonometry',
            'geometry', 'vector', 'algebra', 'calculus', 'rajdhani', 'uttar', 'pradesh', 'google',
            'name', 'learn', 'about', 'what', 'where', 'who', 'how', 'question', 'answer',
        ];
        $out = [];
        foreach ($fallbackWords as $word) {
            $out[$word] = true;
        }
        return $out;
    }
}
