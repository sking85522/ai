<?php
require_once __DIR__ . '/../bootstrap.php';

$root = $argv[1] ?? (__DIR__ . '/../downloadaitrainfile');
if (!is_dir($root)) {
    fwrite(STDERR, "Folder not found: {$root}\n");
    exit(1);
}

$pdo = Db::pdo();
if (!$pdo) {
    fwrite(STDERR, "DB connection failed.\n");
    exit(1);
}

$insertTraining = $pdo->prepare(
    'INSERT INTO training_data (input_text, expected_intent, language_tag, confidence_hint, source, created_at, updated_at)
     VALUES (:i, :e, :l, :c, :s, NOW(), NOW())'
);
$upsertQA = $pdo->prepare(
    'INSERT INTO knowledge_entities (name, type, language_tag, metadata, created_at, updated_at)
     VALUES (:n, :t, :l, :m, NOW(), NOW())
     ON DUPLICATE KEY UPDATE metadata = VALUES(metadata), language_tag = VALUES(language_tag), updated_at = NOW()'
);

$stats = [
    'files_seen' => 0,
    'training_rows' => 0,
    'qa_rows' => 0,
    'skipped_binary' => 0,
    'filtered_noise' => 0,
];
$batch = 0;
$noiseFilter = new CorpusNoiseFilter();

$saveTraining = function (string $input, string $intent, string $lang, float $conf, string $source) use ($insertTraining, &$stats, &$batch, $noiseFilter): void {
    $input = $noiseFilter->normalize($input);
    if (!$noiseFilter->isGoodTrainingText($input)) {
        $stats['filtered_noise']++;
        return;
    }
    $insertTraining->execute([
        ':i' => $input,
        ':e' => $intent,
        ':l' => $lang,
        ':c' => $conf,
        ':s' => $source,
    ]);
    $stats['training_rows']++;
    $batch++;
};

$saveQA = function (string $question, string $answer, string $lang) use ($upsertQA, &$stats, $noiseFilter): void {
    $question = $noiseFilter->normalize($question);
    $answer = $noiseFilter->normalize($answer);
    if (!$noiseFilter->isGoodQA($question, $answer)) {
        $stats['filtered_noise']++;
        return;
    }
    $key = mb_strtolower(trim(preg_replace('/\s+/', ' ', $question) ?? ''), 'UTF-8');
    $meta = json_encode([
        'question' => $question,
        'answer' => $answer,
        'language' => $lang,
        'updated_at' => date('c'),
    ], JSON_UNESCAPED_UNICODE);
    $upsertQA->execute([
        ':n' => $key,
        ':t' => 'qa',
        ':l' => $lang,
        ':m' => $meta,
    ]);
    $stats['qa_rows']++;
};

$isLikelyText = function (string $file): bool {
    $fh = @fopen($file, 'rb');
    if (!$fh) {
        return false;
    }
    $chunk = fread($fh, 2048);
    fclose($fh);
    if ($chunk === false || $chunk === '') {
        return true;
    }
    return strpos($chunk, "\0") === false;
};

$commitIfNeeded = function () use (&$pdo, &$batch, &$stats): void {
    if ($batch >= 1000) {
        $pdo->commit();
        $pdo->beginTransaction();
        echo 'Committed training rows: ' . $stats['training_rows'] . " | QA rows: " . $stats['qa_rows'] . PHP_EOL;
        $batch = 0;
    }
};

echo "Starting full training from: {$root}\n";
$pdo->beginTransaction();

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($it as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $file = $fileInfo->getPathname();
    $name = $fileInfo->getFilename();
    $ext = strtolower($fileInfo->getExtension());
    $stats['files_seen']++;

    if (str_contains($file, DIRECTORY_SEPARATOR . '__MACOSX' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    try {
        // SQuAD v2.0
        if ($name === 'train-v2.0.json') {
            $doc = json_decode((string) file_get_contents($file), true);
            $data = $doc['data'] ?? [];
            if (is_array($data)) {
                foreach ($data as $article) {
                    foreach (($article['paragraphs'] ?? []) as $paragraph) {
                        foreach (($paragraph['qas'] ?? []) as $qa) {
                            $q = trim((string) ($qa['question'] ?? ''));
                            $answers = $qa['answers'] ?? [];
                            if ($q === '' || !is_array($answers) || !$answers) {
                                continue;
                            }
                            $a = trim((string) ($answers[0]['text'] ?? ''));
                            if ($a === '') {
                                continue;
                            }
                            $saveTraining($q, 'knowledge_answer', 'en', 0.92, 'all_folder:squad');
                            $saveQA($q, $a, 'en');
                            $commitIfNeeded();
                        }
                    }
                }
            }
            continue;
        }

        // Coding JSON
        if ($name === 'ai_coding_dataset_large.json') {
            $rows = json_decode((string) file_get_contents($file), true);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $q = trim((string) ($row['prompt'] ?? ''));
                    $a = trim((string) ($row['ai_response'] ?? ''));
                    if ($q === '' || $a === '') {
                        continue;
                    }
                    $lang = 'en';
                    $saveTraining($q, 'coding_help', $lang, 0.8, 'all_folder:coding');
                    $saveQA($q, $a, $lang);
                    $commitIfNeeded();
                }
            }
            continue;
        }

        // SNLI/MNLI JSONL
        if ($ext === 'jsonl') {
            $fh = fopen($file, 'rb');
            if ($fh) {
                while (($line = fgets($fh)) !== false) {
                    $obj = json_decode(trim($line), true);
                    if (!is_array($obj)) {
                        continue;
                    }
                    $s1 = trim((string) ($obj['sentence1'] ?? ''));
                    $s2 = trim((string) ($obj['sentence2'] ?? ''));
                    $label = trim((string) ($obj['gold_label'] ?? 'unknown'));
                    if ($s1 === '' && $s2 === '') {
                        continue;
                    }
                    $input = 'Premise: ' . $s1 . ' Hypothesis: ' . $s2;
                    $intent = 'nli_' . preg_replace('/[^a-z_]+/i', '_', strtolower($label));
                    $saveTraining($input, $intent, 'en', 0.65, 'all_folder:' . $name);
                    $commitIfNeeded();
                }
                fclose($fh);
            }
            continue;
        }

        // Newsgroups raw message files (no extension or numeric names)
        if ($ext === '' || ctype_digit($name)) {
            if (str_contains($file, '20news-') || str_contains($file, '20_newsgroups')) {
                if (!$isLikelyText($file)) {
                    $stats['skipped_binary']++;
                    continue;
                }
                $raw = (string) file_get_contents($file);
                $raw = trim($raw);
                if ($raw === '') {
                    continue;
                }
                $subject = '';
                if (preg_match('/^Subject:\s*(.+)$/mi', $raw, $m) === 1) {
                    $subject = trim((string) $m[1]);
                }
                $parts = preg_split("/\R\R/", $raw, 2);
                $body = trim((string) ($parts[1] ?? $raw));
                $body = preg_replace('/\s+/', ' ', $body) ?? '';
                $body = mb_substr($body, 0, 700, 'UTF-8');
                if ($body === '') {
                    continue;
                }
                $q = $subject !== '' ? $subject : ('topic: ' . basename(dirname($file)));
                $saveTraining($q, 'news_topic', 'en', 0.62, 'all_folder:newsgroup');
                $saveQA($q, $body, 'en');
                $commitIfNeeded();
            }
            continue;
        }

        // JSON files from MSMARCO sample etc
        if ($ext === 'json') {
            if (!$isLikelyText($file)) {
                $stats['skipped_binary']++;
                continue;
            }
            $content = (string) file_get_contents($file);
            $obj = json_decode($content, true);
            if (is_array($obj)) {
                $flat = json_encode($obj, JSON_UNESCAPED_UNICODE);
                if ($flat && strlen($flat) > 10) {
                    $input = 'dataset_file: ' . $name;
                    $answer = mb_substr($flat, 0, 1000, 'UTF-8');
                    $saveTraining($input, 'dataset_json', 'en', 0.55, 'all_folder:json');
                    $saveQA($input, $answer, 'en');
                    $commitIfNeeded();
                }
            }
            continue;
        }

        // Generic TXT/MD files
        if (in_array($ext, ['txt', 'md'], true)) {
            if (!$isLikelyText($file)) {
                $stats['skipped_binary']++;
                continue;
            }
            $fh = fopen($file, 'rb');
            if ($fh) {
                while (($line = fgets($fh)) !== false) {
                    $line = $noiseFilter->normalize($line);
                    if ($line === '' || strlen($line) < 4) {
                        continue;
                    }
                    $saveTraining($line, 'text_corpus', 'en', 0.5, 'all_folder:' . $name);
                    $commitIfNeeded();
                }
                fclose($fh);
            }
            continue;
        }
    } catch (Throwable $e) {
        // Continue with next file on parse errors
        continue;
    }
}

$pdo->commit();
echo "Training complete.\n";
echo 'files_seen=' . $stats['files_seen'] . PHP_EOL;
echo 'training_rows=' . $stats['training_rows'] . PHP_EOL;
echo 'qa_rows=' . $stats['qa_rows'] . PHP_EOL;
echo 'skipped_binary=' . $stats['skipped_binary'] . PHP_EOL;
echo 'filtered_noise=' . $stats['filtered_noise'] . PHP_EOL;
