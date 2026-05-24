<?php
namespace Core\Memory;

/**
 * HRITIK AI - ONLINE CLOUD MEMORY
 * Integrates with the remote database for infinite storage capacity.
 */
class OnlineCloudMemory {
    
    private \Core\Memory\BufferedCloudDB $db;
    private string $rootDir;

    public function __construct() {
        require_once __DIR__ . '/BufferedCloudDB.php';
        $this->db = new \Core\Memory\BufferedCloudDB();
        $this->rootDir = dirname(__DIR__, 2);
    }

    /**
     * Saves a conversation pattern to the online database via the local buffer.
     */
    public function saveMemory(string $prompt, string $response): void {
        if ($this->isUnhelpfulResponse($response)) {
            return;
        }

        $this->db->bufferInsert('neural_memory', [
            'prompt' => trim($prompt),
            'response' => trim($response)
        ]);
    }

    /**
     * Searches both remote DB and local unflushed buffers.
     */
    public function searchMemory(string $prompt): ?string {
        $prompt = strtolower(trim($prompt));
        $keywords = $this->extractKeywords($prompt);

        // Keep remote tables warm so recent learning is searchable.
        $this->db->flushIfPending('knowledge_memory');
        $this->db->flushIfPending('neural_memory');

        foreach ($this->promptVariants($prompt) as $variant) {
            $exactRemote = $this->queryRemoteBestValue(
                "SELECT response FROM neural_memory WHERE prompt = '" . addslashes($variant) . "' LIMIT 5",
                'response'
            );
            if ($this->isUsableCandidate($exactRemote)) {
                return $exactRemote;
            }
        }

        $variants = $this->promptVariants($prompt);
        $exactLocal = $this->searchLocalBuffer('neural_memory', function (array $row) use ($variants) {
            return in_array(strtolower(trim((string)($row['prompt'] ?? ''))), $variants, true);
        });
        if ($this->isUsableCandidate($exactLocal)) {
            return $exactLocal;
        }

        $definitionTopic = $this->inferDefinitionTopic($prompt);
        if ($definitionTopic !== null) {
            foreach ([
                "what is {$definitionTopic}?",
                "what is {$definitionTopic}",
                "entity::{$definitionTopic}",
            ] as $variant) {
                $definition = $this->queryRemoteBestValue(
                    "SELECT response FROM neural_memory WHERE prompt = '" . addslashes($variant) . "' LIMIT 5",
                    'response'
                );
                if ($this->isUsableCandidate($definition)) {
                    return $definition;
                }
            }
        }

        if (empty($keywords)) {
            return null;
        }

        $neuralAnd = $this->queryRemoteBestValue(
            "SELECT response FROM neural_memory WHERE (" . $this->buildLikeClause(['prompt', 'response'], $keywords, 'AND') . ") LIMIT 5",
            'response'
        );
        if ($this->isUsableCandidate($neuralAnd)) {
            return $neuralAnd;
        }

        $neuralOr = $this->queryRemoteBestValue(
            "SELECT response FROM neural_memory WHERE (" . $this->buildLikeClause(['prompt', 'response'], $keywords, 'OR') . ") LIMIT 5",
            'response'
        );
        if ($this->isUsableCandidate($neuralOr)) {
            return $neuralOr;
        }

        $rangeCandidate = $this->searchRemoteNeuralByRanges($keywords);
        if ($this->isUsableCandidate($rangeCandidate)) {
            return $rangeCandidate;
        }

        $knowledgeAnd = $this->queryRemoteBestValue(
            "SELECT content FROM knowledge_memory WHERE (" . $this->buildLikeClause(['topic', 'content'], $keywords, 'AND') . ") LIMIT 5",
            'content'
        );
        if ($this->isUsableCandidate($knowledgeAnd)) {
            return $knowledgeAnd;
        }

        $knowledgeOr = $this->queryRemoteBestValue(
            "SELECT content FROM knowledge_memory WHERE (" . $this->buildLikeClause(['topic', 'content'], $keywords, 'OR') . ") LIMIT 5",
            'content'
        );
        if ($this->isUsableCandidate($knowledgeOr)) {
            return $knowledgeOr;
        }

        $localNeural = $this->searchLocalBuffer('neural_memory', function (array $row) use ($keywords) {
            $haystack = strtolower(((string)($row['prompt'] ?? '')) . ' ' . ((string)($row['response'] ?? '')));
            foreach ($keywords as $kw) {
                if (!str_contains($haystack, $kw)) {
                    return false;
                }
            }
            return true;
        });
        if ($this->isUsableCandidate($localNeural)) {
            return $localNeural;
        }

        $localKnowledge = $this->searchLocalBuffer('knowledge_memory', function (array $row) use ($keywords) {
            $haystack = strtolower(((string)($row['topic'] ?? '')) . ' ' . ((string)($row['content'] ?? '')));
            foreach ($keywords as $kw) {
                if (str_contains($haystack, $kw)) {
                    return true;
                }
            }
            return false;
        }, 'content');
        if ($this->isUsableCandidate($localKnowledge)) {
            return $localKnowledge;
        }

        return null;
    }

    private function queryRemoteBestValue(string $sql, string $field): ?string {
        $res = $this->db->query($sql);
        if (!isset($res['status']) || $res['status'] !== 'success' || empty($res['data'])) {
            return null;
        }

        foreach ($res['data'] as $row) {
            if (empty($row[$field])) {
                continue;
            }

            $candidate = $this->normalizeCandidate((string)$row[$field]);
            if ($this->isUsableCandidate($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function extractKeywords(string $prompt): array {
        static $stopWords = ['is'=>true, 'the'=>true, 'a'=>true, 'an'=>true, 'me'=>true, 'my'=>true, 'what'=>true, 'who'=>true, 'how'=>true, 'where'=>true, 'kyu'=>true, 'kaise'=>true, 'btao'=>true, 'batano'=>true, 'kya'=>true, 'hai'=>true, 'ki'=>true, 'ka'=>true, 'ke'=>true, 'aur'=>true, 'mein'=>true, 'main'=>true, 'to'=>true, 'of'=>true];
        $words = preg_split('/\s+/', $prompt);

        return array_values(array_filter($words, function ($w) use ($stopWords) {
            return strlen($w) > 2 && !isset($stopWords[$w]);
        }));
    }

    private function buildLikeClause(array $fields, array $keywords, string $joiner): string {
        $parts = [];
        foreach ($keywords as $kw) {
            $kwSafe = addslashes($kw);
            $fieldParts = [];
            foreach ($fields as $field) {
                $fieldParts[] = "{$field} LIKE '%{$kwSafe}%'";
            }
            $parts[] = '(' . implode(' OR ', $fieldParts) . ')';
        }

        return implode(" {$joiner} ", $parts);
    }

    private function searchLocalBuffer(string $table, callable $matcher, string $valueKey = 'response'): ?string {
        $bufferFile = $this->rootDir . "/localstorage/buffer_{$table}.jsonl";
        if (!is_file($bufferFile)) {
            return null;
        }

        $lines = @file($bufferFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return null;
        }

        for ($i = count($lines) - 1; $i >= 0; $i--) {
            $row = json_decode($lines[$i], true);
            if (!is_array($row)) {
                continue;
            }

            if ($matcher($row)) {
                return isset($row[$valueKey]) ? $this->normalizeCandidate((string)$row[$valueKey]) : null;
            }
        }

        return null;
    }

    private function promptVariants(string $prompt): array {
        $variants = [$prompt];
        $trimmed = rtrim($prompt, " ?!.\t\n\r\0\x0B");
        if ($trimmed !== $prompt) {
            $variants[] = $trimmed;
        }

        if (!str_ends_with($trimmed, '?')) {
            $variants[] = $trimmed . '?';
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private function inferDefinitionTopic(string $prompt): ?string {
        if (preg_match('/^what is ([a-z0-9\-_ ]+)$/i', trim($prompt), $m)) {
            return trim(strtolower($m[1]));
        }

        if (preg_match('/^([a-z0-9\-_ ]+)\s+kya h(?:ai)?$/i', trim($prompt), $m)) {
            return trim(strtolower($m[1]));
        }

        if (preg_match('/^([a-z0-9\-_ ]+)\s+meaning$/i', trim($prompt), $m)) {
            return trim(strtolower($m[1]));
        }

        return null;
    }

    private function searchRemoteNeuralByRanges(array $keywords): ?string {
        if (empty($keywords)) {
            return null;
        }

        $ranges = [
            [4000000, 5300000],
            [1, 1000000],
            [5300001, 8146590],
        ];

        foreach ($ranges as $range) {
            $sql = "SELECT prompt, response
                    FROM neural_memory
                    WHERE id BETWEEN {$range[0]} AND {$range[1]}
                    AND (" . $this->buildLikeClause(['prompt', 'response'], $keywords, 'OR') . ")
                    LIMIT 20";

            $res = $this->db->query($sql);
            if (!isset($res['status']) || $res['status'] !== 'success' || empty($res['data'])) {
                continue;
            }

            $bestCandidate = null;
            $bestScore = -1;
            foreach ($res['data'] as $row) {
                $candidate = $this->normalizeCandidate((string)($row['response'] ?? ''));
                if (!$this->isUsableCandidate($candidate)) {
                    continue;
                }

                $score = $this->scoreCandidate(
                    strtolower((string)($row['prompt'] ?? '')),
                    strtolower((string)($row['response'] ?? '')),
                    $keywords
                );

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestCandidate = $candidate;
                }
            }

            if ($bestCandidate !== null) {
                return $bestCandidate;
            }
        }

        return null;
    }

    private function scoreCandidate(string $prompt, string $response, array $keywords): int {
        $score = 0;
        foreach ($keywords as $kw) {
            if (str_contains($prompt, $kw)) {
                $score += 5;
            }
            if (str_contains($response, $kw)) {
                $score += 2;
            }
        }

        return $score;
    }

    private function normalizeCandidate(string $candidate): string {
        $candidate = trim($candidate);
        if ($candidate === '') {
            return '';
        }

        $decoded = json_decode($candidate, true);
        if (is_array($decoded)) {
            foreach (['answer', 'content', 'response', 'summary', 'label'] as $field) {
                if (!empty($decoded[$field]) && is_string($decoded[$field])) {
                    return trim($decoded[$field]);
                }
            }
        }

        return $candidate;
    }

    private function isUsableCandidate(?string $candidate): bool {
        if ($candidate === null || $this->isUnhelpfulResponse($candidate)) {
            return false;
        }

        return !(preg_match('/\d+ [\+\-\*\/] \d+ = \d+/u', $candidate) || (str_contains($candidate, '=') && str_contains($candidate, 'Ã—')));
    }

    private function isUnhelpfulResponse(string $response): bool {
        $normalized = strtolower(trim($response));
        if ($normalized === '') {
            return true;
        }

        $blockedPhrases = [
            'main is baare mein thoda aur seekh raha hoon',
            'kya aap mujhe iska sahi jawab bata sakte hain',
            'not learned enough',
            'fact check:',
        ];

        foreach ($blockedPhrases as $phrase) {
            if (str_contains($normalized, $phrase)) {
                return true;
            }
        }

        if (in_array($normalized, ['qa', 'general', 'php'], true)) {
            return true;
        }

        return false;
    }
}
