<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$sourceRoot = dirname(__DIR__) . '/downloadaitrainfile';
$outputDir = dirname(__DIR__) . '/storage/training/json_pack';

$builder = new JsonTrainingPackBuilder();
$result = $builder->build($sourceRoot, $outputDir);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

