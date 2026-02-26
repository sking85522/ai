<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$packDir = dirname(__DIR__) . '/storage/training/json_pack';

$importer = new JsonTrainingPackImporter();
$result = $importer->import($packDir, 'cli_json_pack');

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

