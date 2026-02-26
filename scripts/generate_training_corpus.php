<?php
// Usage: php scripts/generate_training_corpus.php 100
// Generates approx N MB bilingual txt corpus for training.

$targetMb = isset($argv[1]) ? max(1, (int) $argv[1]) : 10;
$targetBytes = $targetMb * 1024 * 1024;
$out = __DIR__ . '/../storage/training/generated_corpus_' . $targetMb . 'mb.txt';

$topics = [
    'ai', 'machine learning', 'deep learning', 'database', 'php', 'memory', 'knowledge graph',
    'hindi english bilingual chat', 'intent detection', 'pattern matching'
];

$fh = fopen($out, 'wb');
if (!$fh) {
    fwrite(STDERR, "Could not open output file\n");
    exit(1);
}

$i = 1;
while (ftell($fh) < $targetBytes) {
    $topic = $topics[array_rand($topics)];
    $q = "What is {$topic} {$i}?";
    $a = "{$topic} ke bare me simple explanation: yeh AI learning pipeline ka important hissa hai.";
    $line = $q . ' => ' . $a . " | general | bilingual\n";
    fwrite($fh, $line);
    $i++;
}

fclose($fh);
echo "Generated: {$out}\n";
echo "Size bytes: " . filesize($out) . "\n";
