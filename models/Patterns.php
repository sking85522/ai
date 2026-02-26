<?php
class Patterns
{
    public function upsert(string $intent, string $token, int $weight = 1): bool
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return false;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO patterns (intent, token, weight, updated_at) VALUES (:i, :t, :w, NOW())
             ON DUPLICATE KEY UPDATE weight = weight + :wu, updated_at = NOW()'
        );

        return $stmt->execute([
            ':i' => $intent,
            ':t' => $token,
            ':w' => $weight,
            ':wu' => $weight,
        ]);
    }

    public function byIntent(string $intent): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return [];
        }
        $stmt = $pdo->prepare('SELECT token, weight FROM patterns WHERE intent = :i ORDER BY weight DESC');
        $stmt->execute([':i' => $intent]);
        return $stmt->fetchAll();
    }

    public function stats(): array
    {
        $pdo = Db::pdo();
        if (!$pdo) {
            return ['total' => 0];
        }
        $total = (int) $pdo->query('SELECT COUNT(*) FROM patterns')->fetchColumn();
        return ['total' => $total];
    }
}
