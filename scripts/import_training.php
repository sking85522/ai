<?php
require_once __DIR__ . '/../bootstrap.php';

$files = array_slice($argv, 1);
if (!$files) {
    $files = [
        __DIR__ . '/../storage/training/seed_knowledge.json',
        __DIR__ . '/../storage/training/seed_knowledge.txt',
        __DIR__ . '/../storage/training/seed_knowledge.xml',
    ];
}

$ingestor = new KnowledgeIngestor();
$total = 0;
foreach ($files as $file) {
    $res = $ingestor->ingestFile($file, 'cli_import');
    echo basename($file) . ' -> ' . $res['message'] . ' | count=' . $res['count'] . PHP_EOL;
    $total += (int) $res['count'];
}

echo 'Total ingested rows: ' . $total . PHP_EOL;
