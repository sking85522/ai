<?php
class Conversation
{
    public function log(
        string $userInput,
        string $aiResponse,
        string $intent,
        float $confidence,
        string $inputLanguage = 'unknown',
        string $responseLanguage = 'unknown',
        array $contextSnapshot = [],
        array $tokens = []
    ): bool
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }
        $stmt = $pdo->prepare(
            'INSERT INTO conversations (
                user_input, ai_response, intent, confidence, input_language, response_language, context_snapshot, tokens, created_at
            ) VALUES (
                :u, :a, :i, :c, :il, :rl, :ctx, :tok, NOW()
            )'
        );
        return $stmt->execute([
            ':u' => $userInput,
            ':a' => $aiResponse,
            ':i' => $intent,
            ':c' => $confidence,
            ':il' => $inputLanguage,
            ':rl' => $responseLanguage,
            ':ctx' => json_encode($contextSnapshot, JSON_UNESCAPED_UNICODE),
            ':tok' => json_encode($tokens, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function recent(int $limit = 20): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return [];
        }
        $stmt = $pdo->prepare('SELECT * FROM conversations ORDER BY id DESC LIMIT :l');
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function stats(): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return ['total' => 0, 'today' => 0];
        }

        $total = (int) $pdo->query('SELECT COUNT(*) FROM conversations')->fetchColumn();
        $todayStmt = $pdo->query('SELECT COUNT(*) FROM conversations WHERE DATE(created_at) = CURRENT_DATE()');
        $today = (int) $todayStmt->fetchColumn();

        return ['total' => $total, 'today' => $today];
    }
}
