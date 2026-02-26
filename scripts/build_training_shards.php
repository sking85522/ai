<?php
require_once __DIR__ . '/../bootstrap.php';

$targetMb = isset($argv[1]) ? max(1, (int) $argv[1]) : 200;
$maxShardBytes = isset($argv[2]) ? max(65536, (int) $argv[2]) : CorpusShardManager::MAX_SHARD_BYTES;
$targetBytes = $targetMb * 1024 * 1024;

$outputRoot = __DIR__ . '/../storage/training/training_shards';
$corpusDir = $outputRoot . '/corpus';
$langDir = $outputRoot . '/lang_main';
$sourceFile = __DIR__ . '/../storage/training/generated_corpus_100mb.txt';

@mkdir($corpusDir, 0777, true);
@mkdir($langDir, 0777, true);

$manager = new CorpusShardManager();

foreach (glob($corpusDir . '/*.txt') ?: [] as $old) {
    @unlink($old);
}
foreach (glob($langDir . '/*.txt') ?: [] as $old) {
    @unlink($old);
}

if (is_file($sourceFile)) {
    $split = $manager->splitTextFile($sourceFile, $corpusDir, $maxShardBytes);
    echo "Base split: " . json_encode($split, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

$totalBytes = 0;
foreach ($manager->listShardFiles($corpusDir) as $file) {
    $totalBytes += filesize($file) ?: 0;
}

$topics = ['math', 'physics', 'vector', 'geometry', 'trigonometry', 'coding', 'database', 'neural network', 'machine learning'];
$i = 1;
while ($totalBytes < $targetBytes) {
    $fileIndex = count($manager->listShardFiles($corpusDir)) + 1;
    $shardPath = $corpusDir . DIRECTORY_SEPARATOR . sprintf('shard_%06d.txt', $fileIndex);
    $fh = fopen($shardPath, 'wb');
    if (!$fh) {
        break;
    }

    $written = 0;
    while ($written < $maxShardBytes && $totalBytes < $targetBytes) {
        $topic = $topics[array_rand($topics)];
        $q = "Explain {$topic} concept {$i} with quick examples";
        $a = "Topic {$topic} summary {$i}: fundamentals, formula, use-cases, and short practice task.";
        $line = $q . ' => ' . $a . " | knowledge_answer | en\n";
        if (($written + strlen($line)) > $maxShardBytes) {
            break;
        }
        fwrite($fh, $line);
        $size = strlen($line);
        $written += $size;
        $totalBytes += $size;
        $i++;
    }
    fclose($fh);
}

$langSources = [
    __DIR__ . '/../lang-main/locales/en/json.json' => 'en',
    __DIR__ . '/../lang-main/locales/hi/json.json' => 'hi',
];

foreach ($langSources as $file => $tag) {
    if (!is_file($file)) {
        continue;
    }
    $data = json_decode((string) file_get_contents($file), true);
    if (!is_array($data)) {
        continue;
    }

    $part = 1;
    $currentBytes = 0;
    $out = fopen($langDir . DIRECTORY_SEPARATOR . "{$tag}_part_" . sprintf('%04d', $part) . '.txt', 'wb');
    if (!$out) {
        continue;
    }

    foreach ($data as $k => $v) {
        if (!is_string($k) || !is_string($v) || trim($k) === '' || trim($v) === '') {
            continue;
        }
        $line = $k . ' => ' . $v . " | language_map | {$tag}\n";
        $lineBytes = strlen($line);
        if (($currentBytes + $lineBytes) > $maxShardBytes) {
            fclose($out);
            $part++;
            $currentBytes = 0;
            $out = fopen($langDir . DIRECTORY_SEPARATOR . "{$tag}_part_" . sprintf('%04d', $part) . '.txt', 'wb');
            if (!$out) {
                break;
            }
        }
        fwrite($out, $line);
        $currentBytes += $lineBytes;
    }

    if (is_resource($out)) {
        fclose($out);
    }
}

$corpusShards = count($manager->listShardFiles($corpusDir));
$langShards = count($manager->listShardFiles($langDir));

echo "Training shard build complete." . PHP_EOL;
echo "Target MB: {$targetMb}" . PHP_EOL;
echo "Corpus shards: {$corpusShards}" . PHP_EOL;
echo "Lang shards: {$langShards}" . PHP_EOL;
