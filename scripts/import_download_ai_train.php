<?php
require_once __DIR__ . '/../bootstrap.php';

$folder = $argv[1] ?? (__DIR__ . '/../downloadaitrainfile');
$coding = isset($argv[2]) ? (int) $argv[2] : 1500;
$squad = isset($argv[3]) ? (int) $argv[3] : 8000;
$news = isset($argv[4]) ? (int) $argv[4] : 2000;

if (!is_dir($folder)) {
    fwrite(STDERR, "Folder not found: {$folder}\n");
    exit(1);
}

$importer = new DownloadedDatasetImporter();
$stats = $importer->importFolder($folder, [
    'coding' => max(0, $coding),
    'squad' => max(0, $squad),
    'news' => max(0, $news),
]);

echo "Imported from folder: {$folder}\n";
echo "coding: {$stats['coding']}\n";
echo "squad: {$stats['squad']}\n";
echo "news: {$stats['news']}\n";
if (!empty($stats['errors'])) {
    echo "errors:\n";
    foreach ($stats['errors'] as $err) {
        echo "- {$err}\n";
    }
}
