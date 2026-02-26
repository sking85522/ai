<?php
require_once __DIR__ . '/../bootstrap.php';

$pdo = Db::pdo();
if (!$pdo) {
    fwrite(STDERR, "DB connection failed.\n");
    exit(1);
}

$filter = new CorpusNoiseFilter();
$batchSize = 5000;

$cleanTable = function (
    string $table,
    string $selectSql,
    callable $isNoise,
    string $label
) use ($pdo, $batchSize): int {
    $cursor = 0;
    $deleted = 0;

    while (true) {
        $stmt = $pdo->prepare($selectSql);
        $stmt->bindValue(':id', $cursor, PDO::PARAM_INT);
        $stmt->bindValue(':l', $batchSize, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        if (!$rows) {
            break;
        }

        $deleteIds = [];
        foreach ($rows as $row) {
            if ($isNoise($row)) {
                $deleteIds[] = (int) $row['id'];
            }
        }

        if ($deleteIds) {
            $ph = implode(',', array_fill(0, count($deleteIds), '?'));
            $del = $pdo->prepare("DELETE FROM {$table} WHERE id IN ({$ph})");
            $del->execute($deleteIds);
            $deleted += count($deleteIds);
        }

        $cursor = (int) end($rows)['id'];
        echo "{$label}: scanned up to ID {$cursor}, deleted {$deleted}\n";
    }

    return $deleted;
};

echo "Starting heavy noise cleanup...\n";

$deletedTraining = $cleanTable(
    'training_data',
    'SELECT id, input_text FROM training_data WHERE id > :id ORDER BY id ASC LIMIT :l',
    static function (array $row) use ($filter): bool {
        return !$filter->isGoodTrainingText((string) ($row['input_text'] ?? ''));
    },
    'training_data'
);

$deletedQA = $cleanTable(
    'knowledge_entities',
    'SELECT id, metadata FROM knowledge_entities WHERE type = "qa" AND id > :id ORDER BY id ASC LIMIT :l',
    static function (array $row) use ($filter): bool {
        $meta = json_decode((string) ($row['metadata'] ?? '{}'), true);
        $q = (string) ($meta['question'] ?? '');
        $a = (string) ($meta['answer'] ?? '');
        return !$filter->isGoodQA($q, $a);
    },
    'knowledge_entities.qa'
);

echo "Cleanup complete.\n";
echo "training_deleted={$deletedTraining}\n";
echo "qa_deleted={$deletedQA}\n";
