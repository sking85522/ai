<?php
class Db
{
    public static function pdo(): ?PDO
    {
        static $pdo = false;
        if ($pdo !== false) {
            return $pdo;
        }

        $db = require __DIR__ . '/../config/database.php';
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['db'],
                $db['charset'] ?? 'utf8mb4'
            );
            $pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (Throwable $e) {
            $pdo = null;
            return null;
        }
    }
}
