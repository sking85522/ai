<?php
class TrainingData
{
    public function add(
        string $inputText,
        string $expectedIntent,
        string $languageTag = 'unknown',
        ?float $confidenceHint = null,
        ?string $source = null
    ): bool {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at)
             VALUES (:i, :e, :l, :c, :s, NOW(), NOW())'
        );
        return $stmt->execute([
            ':i' => $inputText,
            ':e' => $expectedIntent,
            ':l' => $this->normalizeLanguage($languageTag),
            ':c' => $confidenceHint,
            ':s' => $source,
        ]);
    }

    public function all(int $limit = 100): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return [];
        }
        $stmt = $pdo->prepare('SELECT * FROM training_data ORDER BY id DESC LIMIT :l');
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function stats(): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return ['total' => 0];
        }
        $total = (int) $pdo->query('SELECT COUNT(*) FROM training_data')->fetchColumn();
        return ['total' => $total];
    }

    private function normalizeLanguage(string $language): string
    {
        $allowed = ['en', 'hi', 'bilingual', 'unknown'];
        return in_array($language, $allowed, true) ? $language : 'unknown';
    }
}
