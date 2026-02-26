<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$packDir = dirname(__DIR__) . '/storage/training/json_pack';
$batchSize = 250;

if (isset($argv[1]) && trim((string) $argv[1]) !== '') {
    $packDir = (string) $argv[1];
}
if (isset($argv[2]) && is_numeric($argv[2])) {
    $batchSize = max(1, (int) $argv[2]);
}

$exporter = new JsonPackSqlExporter();
$result = $exporter->export($packDir, $batchSize);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
