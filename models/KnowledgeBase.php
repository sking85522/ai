<?php
class KnowledgeBase
{
    public function addEntity(string $name, string $type = 'general', string $language = 'unknown', ?array $metadata = null): ?int
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return null;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
             VALUES (:n, :t, :l, :m, NOW(), NOW())'
        );
        $ok = $stmt->execute([
            ':n' => $name,
            ':t' => $type,
            ':l' => $this->normalizeLanguage($language),
            ':m' => $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null,
        ]);

        if (!$ok) {
            return null;
        }
        return (int) $pdo->lastInsertId();
    }

    public function upsertQA(string $question, string $answer, string $language = 'bilingual'): bool
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }

        $key = $this->normalizeKey($question);
        $metadata = json_encode([
            'question' => $question,
            'answer' => $answer,
            'language' => $this->normalizeLanguage($language),
            'updated_at' => date('c'),
        ], JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare(
            'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
             VALUES (:n, :t, :l, :m, NOW(), NOW())
             ON DUPLICATE KEY UPDATE language_tag = VALUES(language_tag), metadata = VALUES(metadata), updated_at = NOW()'
        );

        return $stmt->execute([
            ':n' => $key,
            ':t' => 'qa',
            ':l' => $this->normalizeLanguage($language),
            ':m' => $metadata,
        ]);
    }

    public function upsertResponsePolicy(string $trigger, string $response, string $language = 'bilingual', string $mode = 'contains'): bool
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }

        $mode = in_array($mode, ['contains', 'exact'], true) ? $mode : 'contains';
        $key = 'policy::' . $this->normalizeKey($trigger);
        $metadata = json_encode([
            'trigger' => $trigger,
            'response' => $response,
            'language' => $this->normalizeLanguage($language),
            'mode' => $mode,
            'updated_at' => date('c'),
        ], JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare(
            'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
             VALUES (:n, :t, :l, :m, NOW(), NOW())
             ON DUPLICATE KEY UPDATE metadata = VALUES(metadata), language_tag = VALUES(language_tag), updated_at = NOW()'
        );

        return $stmt->execute([
            ':n' => $key,
            ':t' => 'response_policy',
            ':l' => $this->normalizeLanguage($language),
            ':m' => $metadata,
        ]);
    }

    public function findResponsePolicy(string $input): ?array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return null;
        }

        $stmt = $pdo->prepare(
            'SELECT metadata FROM knowledge_entities
             WHERE type = :t
             ORDER BY updated_at DESC
             LIMIT 500'
        );
        $stmt->execute([':t' => 'response_policy']);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return null;
        }

        $normalized = $this->normalizeKey($input);
        foreach ($rows as $row) {
            $meta = json_decode((string) ($row['metadata'] ?? '{}'), true);
            $trigger = $this->normalizeKey((string) ($meta['trigger'] ?? ''));
            $response = (string) ($meta['response'] ?? '');
            $mode = (string) ($meta['mode'] ?? 'contains');
            if ($trigger === '' || $response === '') {
                continue;
            }
            if ($mode === 'exact' && $normalized === $trigger) {
                return $meta;
            }
            if ($mode !== 'exact' && str_contains($normalized, $trigger)) {
                return $meta;
            }
        }
        return null;
    }

    public function findBestQA(string $query, int $limit = 200): ?array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return null;
        }
        $noise = new CorpusNoiseFilter();

        $queryNorm = $this->normalizeKey($query);
        if ($queryNorm === '') {
            return null;
        }

        $exactStmt = $pdo->prepare(
            'SELECT metadata, language_tag
             FROM knowledge_entities
             WHERE type = :t AND name = :n
             LIMIT 1'
        );
        $exactStmt->execute([':t' => 'qa', ':n' => $queryNorm]);
        $exact = $exactStmt->fetch();
        if ($exact) {
            $meta = json_decode((string) ($exact['metadata'] ?? '{}'), true);
            $answer = (string) ($meta['answer'] ?? '');
            if ($noise->isGoodQA((string) ($meta['question'] ?? $queryNorm), $answer)) {
                return [
                    'question' => (string) ($meta['question'] ?? $queryNorm),
                    'answer' => $answer,
                    'language' => (string) ($meta['language'] ?? $exact['language_tag'] ?? 'en'),
                    'score' => 1.0,
                ];
            }
        }

        $tokens = array_values(array_filter(explode(' ', $queryNorm), static fn($t) => strlen($t) >= 3));
        $tokens = array_slice($tokens, 0, 6);

        $rows = [];
        if ($tokens) {
            $clauses = [];
            $params = [':t' => 'qa'];
            foreach ($tokens as $i => $token) {
                $key = ':k' . $i;
                $clauses[] = "name LIKE {$key}";
                $params[$key] = '%' . $token . '%';
            }
            $sql = 'SELECT name, language_tag, metadata FROM knowledge_entities WHERE type = :t AND (' . implode(' OR ', $clauses) . ') ORDER BY updated_at DESC LIMIT :l';
            $stmt = $pdo->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
            $stmt->bindValue(':l', max(500, $limit), PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        }

        if (!$rows) {
            $stmt = $pdo->prepare(
                'SELECT name, language_tag, metadata
                 FROM knowledge_entities
                 WHERE type = :t
                 ORDER BY updated_at DESC
                 LIMIT :l'
            );
            $stmt->bindValue(':t', 'qa', PDO::PARAM_STR);
            $stmt->bindValue(':l', max(500, $limit), PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            if (!$rows) {
                return null;
            }
        }

        $best = null;
        $bestScore = 0.0;

        foreach ($rows as $row) {
            $metadata = json_decode((string) ($row['metadata'] ?? '{}'), true);
            $candidate = (string) ($metadata['question'] ?? $row['name']);
            $answer = (string) ($metadata['answer'] ?? '');
            if (!$noise->isGoodQA($candidate, $answer)) {
                continue;
            }
            similar_text($queryNorm, $this->normalizeKey($candidate), $score);
            $score = (float) $score;
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = [
                    'question' => $candidate,
                    'answer' => $answer,
                    'language' => (string) ($metadata['language'] ?? $row['language_tag'] ?? 'bilingual'),
                    'score' => round($score / 100, 4),
                ];
            }
        }

        if (!$best || $best['score'] < 0.66) {
            return null;
        }

        return $best;
    }

    public function upsertUserFact(string $key, string $value, string $language = 'bilingual'): bool
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }

        $entityKey = 'user_fact::' . $this->normalizeKey($key);
        $metadata = json_encode([
            'key' => $key,
            'value' => $value,
            'language' => $this->normalizeLanguage($language),
            'updated_at' => date('c'),
        ], JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare(
            'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
             VALUES (:n, :t, :l, :m, NOW(), NOW())
             ON DUPLICATE KEY UPDATE metadata = VALUES(metadata), language_tag = VALUES(language_tag), updated_at = NOW()'
        );

        return $stmt->execute([
            ':n' => $entityKey,
            ':t' => 'user_fact',
            ':l' => $this->normalizeLanguage($language),
            ':m' => $metadata,
        ]);
    }

    public function findUserFact(string $query): ?array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return null;
        }

        $detectedKey = $this->detectFactKey($query);
        if ($detectedKey !== null) {
            $direct = $pdo->prepare('SELECT metadata FROM knowledge_entities WHERE type = :t AND name = :n LIMIT 1');
            $direct->execute([':t' => 'user_fact', ':n' => 'user_fact::' . $detectedKey]);
            $raw = $direct->fetchColumn();
            if ($raw) {
                $meta = json_decode((string) $raw, true);
                if (!empty($meta['value'])) {
                    return [
                        'key' => (string) ($meta['key'] ?? $detectedKey),
                        'value' => (string) $meta['value'],
                        'language' => (string) ($meta['language'] ?? 'bilingual'),
                        'score' => 0.95,
                    ];
                }
            }
        }

        $stmt = $pdo->prepare(
            'SELECT name, metadata
             FROM knowledge_entities
             WHERE type = :t
             ORDER BY updated_at DESC
             LIMIT 100'
        );
        $stmt->execute([':t' => 'user_fact']);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return null;
        }

        $q = $this->normalizeKey($query);
        $best = null;
        $bestScore = 0.0;
        foreach ($rows as $row) {
            $meta = json_decode((string) ($row['metadata'] ?? '{}'), true);
            $candidate = $this->normalizeKey((string) ($meta['key'] ?? ''));
            similar_text($q, $candidate, $score);
            $score = (float) $score;
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = [
                    'key' => (string) ($meta['key'] ?? ''),
                    'value' => (string) ($meta['value'] ?? ''),
                    'language' => (string) ($meta['language'] ?? 'bilingual'),
                    'score' => round($score / 100, 4),
                ];
            }
        }

        if (!$best || $best['score'] < 0.45 || $best['value'] === '') {
            return null;
        }
        return $best;
    }

    public function addRelation(int $sourceId, string $relation, int $targetId): bool
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }
        $stmt = $pdo->prepare(
            'INSERT INTO knowledge_relations (source_entity_id, relation, target_entity_id, created_at, updated_at)
             VALUES (:s, :r, :t, NOW(), NOW())
             ON DUPLICATE KEY UPDATE updated_at = NOW()'
        );
        return $stmt->execute([':s' => $sourceId, ':r' => $relation, ':t' => $targetId]);
    }

    public function relatedTo(string $entityName): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return [];
        }
        $stmt = $pdo->prepare(
            'SELECT e1.name AS source, kr.relation, e2.name AS target
             FROM knowledge_relations kr
             JOIN knowledge_entities e1 ON e1.id = kr.source_entity_id
             JOIN knowledge_entities e2 ON e2.id = kr.target_entity_id
             WHERE e1.name = :n OR e2.name = :n'
        );
        $stmt->execute([':n' => $entityName]);
        return $stmt->fetchAll();
    }

    public function stats(): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return ['total' => 0, 'qa' => 0, 'facts' => 0, 'policies' => 0];
        }

        $total = (int) $pdo->query('SELECT COUNT(*) FROM knowledge_entities')->fetchColumn();
        $qa = (int) $pdo->query("SELECT COUNT(*) FROM knowledge_entities WHERE type = 'qa'")->fetchColumn();
        $facts = (int) $pdo->query("SELECT COUNT(*) FROM knowledge_entities WHERE type = 'user_fact'")->fetchColumn();
        $policies = (int) $pdo->query("SELECT COUNT(*) FROM knowledge_entities WHERE type = 'response_policy'")->fetchColumn();

        return ['total' => $total, 'qa' => $qa, 'facts' => $facts, 'policies' => $policies];
    }

    private function normalizeKey(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        return $text;
    }

    private function normalizeLanguage(string $language): string
    {
        $allowed = ['en', 'hi', 'bilingual', 'unknown'];
        return in_array($language, $allowed, true) ? $language : 'unknown';
    }

    private function detectFactKey(string $query): ?string
    {
        $q = $this->normalizeKey($query);
        if (str_contains($q, 'my name') || str_contains($q, 'mera naam')) {
            return 'name';
        }
        if (str_contains($q, 'my city') || str_contains($q, 'i live') || str_contains($q, 'kahan')) {
            return 'city';
        }
        if (str_contains($q, 'friend name') || str_contains($q, 'mere friend ka naam')) {
            return 'friend_name';
        }
        if (str_contains($q, 'my phone') || str_contains($q, 'mera number')) {
            return 'phone';
        }
        return null;
    }
}
