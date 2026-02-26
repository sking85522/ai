<?php
require_once __DIR__ . '/../bootstrap.php';

$source = $argv[1] ?? (__DIR__ . '/../storage/training/generated_corpus_100mb.txt');
$maxLines = isset($argv[2]) ? max(1, (int) $argv[2]) : 5000;
$batchSize = isset($argv[3]) ? max(100, (int) $argv[3]) : 500;

if (!is_file($source) && !is_dir($source)) {
    fwrite(STDERR, "Source not found: {$source}\n");
    exit(1);
}

$pdo = Db::pdo();
if (!$pdo) {
    fwrite(STDERR, "DB connection failed.\n");
    exit(1);
}

$insTrain = $pdo->prepare(
    'INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at)
     VALUES (:i, :e, :l, :c, :s, NOW(), NOW())'
);
$insQA = $pdo->prepare(
    'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
     VALUES (:n, :t, :l, :m, NOW(), NOW())
     ON DUPLICATE KEY UPDATE metadata = VALUES(metadata), language_tag = VALUES(language_tag), updated_at = NOW()'
);

$lineCount = 0;
$inserted = 0;
$pdo->beginTransaction();
$files = [];
if (is_dir($source)) {
    $files = glob(rtrim($source, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.txt') ?: [];
    sort($files, SORT_NATURAL);
} else {
    $files = [$source];
}

$processLine = function (string $line) use (&$lineCount, &$inserted, $maxLines, $batchSize, $pdo, $insTrain, $insQA): bool {
    if ($lineCount >= $maxLines) {
        return false;
    }
    $line = trim($line);
    if ($line === '') {
        return true;
    }
    $lineCount++;

    $parts = preg_split('/=>|\|/', $line);
    if (!$parts || count($parts) < 2) {
        return true;
    }
    $q = trim((string) $parts[0]);
    $a = trim((string) $parts[1]);
    $intent = isset($parts[2]) ? trim((string) $parts[2]) : 'general';
    $lang = isset($parts[3]) ? trim((string) $parts[3]) : 'bilingual';

    if ($q === '' || $a === '') {
        return true;
    }

    $insTrain->execute([
        ':i' => $q,
        ':e' => $intent,
        ':l' => $lang,
        ':c' => 0.7,
        ':s' => 'bulk_corpus',
    ]);

    $meta = json_encode([
        'question' => $q,
        'answer' => $a,
        'language' => $lang,
        'updated_at' => date('c'),
    ], JSON_UNESCAPED_UNICODE);

    $insQA->execute([
        ':n' => mb_strtolower($q, 'UTF-8'),
        ':t' => 'qa',
        ':l' => $lang,
        ':m' => $meta,
    ]);
    $inserted++;

    if ($inserted % $batchSize === 0) {
        $pdo->commit();
        $pdo->beginTransaction();
        echo "Committed rows: {$inserted}\n";
    }

    return true;
};

foreach ($files as $file) {
    $fh = fopen($file, 'rb');
    if (!$fh) {
        continue;
    }
    while (!feof($fh)) {
        $line = fgets($fh);
        if ($line === false) {
            break;
        }
        if (!$processLine($line)) {
            break 2;
        }
    }
    fclose($fh);
}

$pdo->commit();

echo "Processed lines: {$lineCount}\n";
echo "Inserted rows: {$inserted}\n";
