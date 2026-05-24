<?php
namespace Core\Memory\RAG;

class LocalRAG {
    // Cache to prevent loading 5000+ rows from the database multiple times per request lifecycle
    private array $cache = [];
    private bool $cacheLoaded = false;

    private array $stopWords = [
        'kya' => true, 'hai' => true, 'h' => true, 'ho' => true, 'hot' => true, 'hoti' => true,
        'ka' => true, 'ki' => true, 'ke' => true, 'ko' => true, 'se' => true, 'me' => true, 'mein' => true,
        'btao' => true, 'batao' => true, 'simple' => true, 'ek' => true, 'the' => true, 'is' => true,
        'are' => true, 'what' => true, 'who' => true, 'how' => true, 'tell' => true,
        'about' => true, 'please' => true, 'plz' => true, 'mujhe' => true, 'tum' => true,
        'tumhara' => true, 'tumara' => true
    ];

    public function __construct(?string $verifiedPath = null, ?string $learnedPath = null) {}

    public function answer(string $query, float $minScore = 0.34): ?array {
        $items = $this->loadAll();
        if (empty($items)) {
            return null;
        }

        $ranked = [];
        foreach ($items as $item) {
            $question = (string)($item['question'] ?? $item['q'] ?? '');
            $answer = (string)($item['answer'] ?? $item['a'] ?? '');
            if ($question === '' || $answer === '' || $this->isWeak($answer)) {
                continue;
            }

            $score = $this->score($query, $question, (array)($item['tags'] ?? []));
            if ($score >= $minScore) {
                $ranked[] = [
                    'question' => $question,
                    'answer' => $answer,
                    'score' => $score,
                    'source' => $item['source'] ?? 'local_verified',
                    'quality' => (float)($item['quality'] ?? 0.8)
                ];
            }
        }

        if (empty($ranked)) {
            return null;
        }

        usort($ranked, fn($a, $b) => $b['score'] <=> $a['score']);
        $best = $ranked[0];

        return [
            'answer' => $best['answer'],
            'confidence' => min(0.96, max(0.55, ($best['score'] * 0.72) + ($best['quality'] * 0.24))),
            'evidence' => array_slice($ranked, 0, 3)
        ];
    }

    public function upsert(string $question, string $answer, string $source = 'local_teacher', float $quality = 0.78): bool {
        $question = trim((string)preg_replace('/^\xEF\xBB\xBF/u', '', $question));
        $answer = trim($answer);
        if ($question === '' || $answer === '' || $this->isWeak($answer)) {
            return false;
        }

        global $db;
        if (!isset($db) || $db === null) {
            return false;
        }

        $safeQuestion = addslashes($question);
        $safeAnswer = addslashes($answer);
        $safeSource = addslashes($source);
        $safeQuality = (float)$quality;
        $safeTags = addslashes(json_encode(array_keys($this->tokens($question))));

        $updateSql = "UPDATE neural_knowledge SET k_value='{$safeAnswer}', sub_category='{$safeSource}', quality_score={$safeQuality}, tags_json='{$safeTags}' " .
                     "WHERE category='verified_qa' AND k_key='{$safeQuestion}' LIMIT 1";
        $updateRes = $db->query($updateSql);
        if (($updateRes['status'] ?? '') === 'error') {
            $updateSql = "UPDATE neural_knowledge SET k_value='{$safeAnswer}', sub_category='{$safeSource}' " .
                         "WHERE category='verified_qa' AND k_key='{$safeQuestion}' LIMIT 1";
            $updateRes = $db->query($updateSql);
        }
        $updated = isset($updateRes['status']) && $updateRes['status'] === 'success' && (($updateRes['affected_rows'] ?? 0) > 0);

        if ($updated) {
            return true;
        }

        $insertSql = "INSERT INTO neural_knowledge (category, sub_category, k_key, k_value, quality_score, tags_json) " .
                     "VALUES ('verified_qa', '{$safeSource}', '{$safeQuestion}', '{$safeAnswer}', {$safeQuality}, '{$safeTags}')";
        $insertRes = $db->query($insertSql);
        if (($insertRes['status'] ?? '') === 'error') {
            $insertSql = "INSERT INTO neural_knowledge (category, sub_category, k_key, k_value) " .
                         "VALUES ('verified_qa', '{$safeSource}', '{$safeQuestion}', '{$safeAnswer}')";
            $insertRes = $db->query($insertSql);
        }
        return isset($insertRes['status']) && $insertRes['status'] === 'success';
    }

    public function isWeak(string $answer): bool {
        $a = strtolower($answer);
        $weak = [
            'study karni', 'exact jawab nahi', 'data bank', 'smarter ho raha', 'time dijiye',
            'training le raha', 'nahi jaanta', 'not found', 'i do not know', 'mujhe iske baare'
        ];

        foreach ($weak as $phrase) {
            if (str_contains($a, $phrase)) {
                return true;
            }
        }

        return strlen(trim($answer)) < 8;
    }

    private function loadAll(): array {
        // Return cached items if already loaded to optimize RAG performance
        if ($this->cacheLoaded) {
            return $this->cache;
        }

        global $db;
        if (!isset($db) || $db === null) {
            return [];
        }

        $sql = "SELECT k_key, k_value, sub_category, quality_score, tags_json FROM neural_knowledge " .
               "WHERE category='verified_qa' AND LENGTH(k_value) > 5 ORDER BY id DESC LIMIT 5000";
        $res = $db->query($sql);
        if (($res['status'] ?? '') === 'error') {
            $sql = "SELECT k_key, k_value, sub_category FROM neural_knowledge " .
                   "WHERE category='verified_qa' AND LENGTH(k_value) > 5 ORDER BY id DESC LIMIT 5000";
            $res = $db->query($sql);
        }
        if (!isset($res['status']) || $res['status'] !== 'success' || empty($res['data'])) {
            return [];
        }

        $items = [];
        foreach ($res['data'] as $row) {
            $tags = json_decode((string)($row['tags_json'] ?? ''), true);
            $items[] = [
                'question' => (string)($row['k_key'] ?? ''),
                'answer' => (string)($row['k_value'] ?? ''),
                'tags' => is_array($tags) ? $tags : [],
                'source' => (string)($row['sub_category'] ?? 'verified_qa'),
                'quality' => (float)($row['quality_score'] ?? 0.82)
            ];
        }

        $this->cache = $items;
        $this->cacheLoaded = true;

        return $items;
    }

    private function score(string $query, string $question, array $tags = []): float {
        $qNorm = $this->normalize($query);
        $questionNorm = $this->normalize($question);

        if ($qNorm === $questionNorm) {
            return 1.0;
        }

        $queryTokens = $this->tokens($query);
        $questionTokens = $this->tokens($question);
        $tagTokens = [];
        foreach ($tags as $tag) {
            foreach ($this->tokens((string)$tag) as $token) {
                $tagTokens[$token] = true;
            }
        }

        if (empty($queryTokens) || empty($questionTokens)) {
            return 0.0;
        }

        $overlap = count(array_intersect_key($queryTokens, $questionTokens));
        $tagOverlap = count(array_intersect_key($queryTokens, $tagTokens));
        $coverage = $overlap / max(1, count($queryTokens));
        $precision = $overlap / max(1, count($questionTokens));
        $containsBoost = (str_contains($questionNorm, $qNorm) || str_contains($qNorm, $questionNorm)) ? 0.2 : 0.0;

        return min(1.0, ($coverage * 0.62) + ($precision * 0.22) + ($tagOverlap * 0.06) + $containsBoost);
    }

    private function tokens(string $text): array {
        $normalized = $this->normalize($text);
        $parts = preg_split('/\s+/', $normalized) ?: [];
        $tokens = [];

        foreach ($parts as $part) {
            if ($part === '' || strlen($part) < 2 || isset($this->stopWords[$part])) {
                continue;
            }
            $tokens[$part] = true;
        }

        return $tokens;
    }

    private function normalize(string $text): string {
        $text = strtolower(trim($text));
        $replace = [
            'bnaya' => 'banaya',
            'bnao' => 'banao',
            'bna' => 'bana',
            'tumara' => 'tumhara',
            'web page' => 'webpage',
            'web-site' => 'website',
            'htmll' => 'html'
        ];
        $text = strtr($text, $replace);
        $text = preg_replace('/[^a-z0-9+#. ]+/i', ' ', $text);
        $text = preg_replace('/\s+/', ' ', (string)$text);
        return trim((string)$text);
    }

}
