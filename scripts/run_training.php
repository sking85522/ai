<?php
require_once __DIR__ . '/../bootstrap.php';

$input = __DIR__ . '/../storage/training/generated_corpus_100mb.txt';
$outDir = __DIR__ . '/../storage/training/training_shards';
$linesPerShard = 10000;

echo "Splitting $input into shards (lines per shard: $linesPerShard)...\n";
$files = ShardGenerator::splitFileByLines($input, $outDir, $linesPerShard);
echo "Created " . count($files) . " shards in $outDir\n";

$pipeline = new TrainingPipeline();
$classifier = new SimpleTextClassifier();
$orc = new TrainingOrchestrator($pipeline, $classifier);

// Smoke-run: process up to 10 shards with modest batch size to validate pipeline
$res = $orc->trainFromShardFiles($outDir, 2000, 10);

echo "Training complete:\n";
print_r($res);
