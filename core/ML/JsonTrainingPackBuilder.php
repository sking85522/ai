<?php
class JsonTrainingPackBuilder
{
    private CorpusNoiseFilter $filter;

    /** @var resource[] */
    private array $handles = [];
    /** @var array<string, int> */
    private array $stats = [];

    public function __construct()
    {
        $this->filter = new CorpusNoiseFilter();
    }

    public function build(string $sourceRoot, string $outputDir): array
    {
        if (!is_dir($sourceRoot)) {
            return ['ok' => false, 'message' => 'Source folder not found'];
        }
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $outputFiles = [
            'training' => $outputDir . '/training_data.jsonl',
            'patterns' => $outputDir . '/patterns.jsonl',
            'weights' => $outputDir . '/neural_weights.jsonl',
            'entities' => $outputDir . '/knowledge_entities.jsonl',
            'relations' => $outputDir . '/knowledge_relations.jsonl',
        ];
        foreach ($outputFiles as $path) {
            file_put_contents($path, '');
        }

        foreach ($outputFiles as $key => $path) {
            $this->handles[$key] = fopen($path, 'ab');
        }

        $this->stats = [
            'training_data' => 0,
            'patterns' => 0,
            'neural_weights' => 0,
            'knowledge_entities' => 0,
            'knowledge_relations' => 0,
        ];
        
        $this->seedBaseData();

        // SQuAD
        $squadPath = $sourceRoot . '/train-v2.0.json';
        if (is_file($squadPath)) {
            $doc = json_decode((string) file_get_contents($squadPath), true);
            foreach (($doc['data'] ?? []) as $article) {
                foreach (($article['paragraphs'] ?? []) as $paragraph) {
                    foreach (($paragraph['qas'] ?? []) as $qa) {
                        if (($qa['is_impossible'] ?? false) === true) {
                            continue;
                        }
                        $q = trim((string) ($qa['question'] ?? ''));
                        $answers = $qa['answers'] ?? [];
                        $a = is_array($answers) && $answers ? trim((string) ($answers[0]['text'] ?? '')) : '';
                        if (!$this->filter->isGoodQA($q, $a)) {
                            continue;
                        }
                        $this->pushTraining($q, 'knowledge_answer', 'en', 0.92, 'jsonpack:squad');
                        $this->pushQA($q, $a, 'en');
                        $this->addPatterns('knowledge_answer', $q);
                    }
                }
            }
        }

        // Coding json
        $codingPath = $sourceRoot . '/ai_coding_dataset_large.json';
        if (is_file($codingPath)) {
            $rows = json_decode((string) file_get_contents($codingPath), true);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $q = trim((string) ($row['prompt'] ?? ''));
                    $a = trim((string) ($row['ai_response'] ?? ''));
                    $lang = strtolower((string) ($row['language'] ?? 'en'));
                    $lang = $lang === 'hindi' ? 'hi' : 'en';
                    if (!$this->filter->isGoodQA($q, $a)) {
                        continue;
                    }
                    $this->pushTraining($q, 'coding_help', $lang, 0.8, 'jsonpack:coding');
                    $this->pushQA($q, $a, $lang);
                    $this->addPatterns('coding_help', $q);
                }
            }
        }

        // JSONL (SNLI/MNLI)
        $jsonlFiles = glob($sourceRoot . '/**/*.jsonl', GLOB_BRACE) ?: [];
        if (!$jsonlFiles) {
            $jsonlFiles = $this->findFilesByExt($sourceRoot, 'jsonl');
        }
        foreach ($jsonlFiles as $file) {
            $fh = fopen($file, 'rb');
            if (!$fh) {
                continue;
            }
            while (($line = fgets($fh)) !== false) {
                $obj = json_decode(trim($line), true);
                if (!is_array($obj)) {
                    continue;
                }
                $s1 = trim((string) ($obj['sentence1'] ?? ''));
                $s2 = trim((string) ($obj['sentence2'] ?? ''));
                $label = trim((string) ($obj['gold_label'] ?? 'unknown'));
                $input = trim('Premise: ' . $s1 . ' Hypothesis: ' . $s2);
                if (!$this->filter->isGoodTrainingText($input)) {
                    continue;
                }
                $intent = 'nli_' . preg_replace('/[^a-z_]+/i', '_', strtolower($label));
                $this->pushTraining($input, $intent, 'en', 0.66, 'jsonpack:' . basename($file));
                $this->addPatterns($intent, $input);
            }
            fclose($fh);
        }

        // Newsgroups text corpus
        $newsRoots = ['20news-18828', '20news-bydate-train', '20news-bydate-test', '20_newsgroups'];
        foreach ($newsRoots as $rootName) {
            $path = $sourceRoot . '/' . $rootName;
            if (!is_dir($path)) {
                continue;
            }
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $fileInfo) {
                if (!$fileInfo->isFile()) {
                    continue;
                }
                $raw = @file_get_contents($fileInfo->getPathname());
                if ($raw === false || $raw === '') {
                    continue;
                }
                $parts = preg_split("/\R\R/", $raw, 2);
                $fullBody = trim((string) ($parts[1] ?? $raw));
                $fullBody = preg_replace('/\s+/', ' ', $fullBody) ?? '';

                $subject = '';
                if (preg_match('/^Subject:\s*(.+)$/mi', $raw, $m) === 1) {
                    $subject = trim((string) $m[1]);
                }
                $q = $subject !== '' ? $subject : ('topic: ' . basename(dirname($fileInfo->getPathname())));

                // Chunk body for RAG (v0.1.0) and add source citation metadata
                $chunks = $this->chunkText($fullBody, 700, 100);
                foreach ($chunks as $i => $chunk) {
                    $chunkQuestion = $q . (count($chunks) > 1 ? " (part " . ($i + 1) . ")" : "");
                    if (!$this->filter->isGoodQA($chunkQuestion, $chunk)) {
                        continue;
                    }
                    $this->pushTraining($chunkQuestion, 'news_topic', 'en', 0.62, 'jsonpack:newsgroup');
                    $this->pushQA($chunkQuestion, $chunk, 'en', [
                        'source' => 'newsgroup:' . $fileInfo->getFilename(),
                        'chunk_id' => $i,
                    ]);
                    $this->addPatterns('news_topic', $chunkQuestion);
                }
            }
        }

        foreach ($this->handles as $fh) {
            fclose($fh);
        }

        file_put_contents($outputDir . '/manifest.json', json_encode([
            'generated_at' => date('c'),
            'source_root' => $sourceRoot,
            'counts' => $stats,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));

        return ['ok' => true, 'message' => 'JSON pack generated', 'counts' => $this->stats, 'output_dir' => $outputDir];
    }

    /** @return string[] */
    private function findFilesByExt(string $root, string $ext): array
    {
        $result = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $fileInfo) {
            if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === strtolower($ext)) {
                $result[] = $fileInfo->getPathname();
            }
        }
        return $result;
    }

    private function writeJsonl($fh, array $row): void
    {
        fwrite($fh, json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL);
    }

    private function seedBaseData(): void
    {
        $baseWeights = [
            ['layer_name' => 'input_hidden', 'from_node' => 0, 'to_node' => 0, 'weight_value' => 0.45, 'bias_value' => 0.1, 'activation' => 'relu', 'version_no' => 1],
            ['layer_name' => 'input_hidden', 'from_node' => 1, 'to_node' => 0, 'weight_value' => 0.52, 'bias_value' => 0.1, 'activation' => 'relu', 'version_no' => 1],
            ['layer_name' => 'hidden_output', 'from_node' => 0, 'to_node' => 0, 'weight_value' => 0.62, 'bias_value' => 0.05, 'activation' => 'sigmoid', 'version_no' => 1],
        ];
        foreach ($baseWeights as $row) {
            $this->writeJsonl($this->handles['weights'], $row);
            $this->stats['neural_weights']++;
        }

        $baseEntities = [
            ['name' => 'entity::php', 'type' => 'general', 'language_tag' => 'en', 'metadata' => ['label' => 'PHP', 'domain' => 'programming']],
            ['name' => 'entity::html', 'type' => 'general', 'language_tag' => 'en', 'metadata' => ['label' => 'HTML', 'domain' => 'web']],
            ['name' => 'entity::css', 'type' => 'general', 'language_tag' => 'en', 'metadata' => ['label' => 'CSS', 'domain' => 'web']],
        ];
        foreach ($baseEntities as $row) {
            $this->writeJsonl($this->handles['entities'], $row);
            $this->stats['knowledge_entities']++;
        }

        $baseRelations = [
            ['source_name' => 'entity::php', 'relation' => 'works_with', 'target_name' => 'entity::html', 'relation_weight' => 1.0],
            ['source_name' => 'entity::html', 'relation' => 'styled_by', 'target_name' => 'entity::css', 'relation_weight' => 1.0],
        ];
        foreach ($baseRelations as $row) {
            $this->writeJsonl($this->handles['relations'], $row);
            $this->stats['knowledge_relations']++;
        }
    }

    private function pushTraining(string $input, string $intent, string $lang, float $conf, string $source): void
    {
        $this->writeJsonl($this->handles['training'], [
            'input_text' => $input,
            'expected_intent' => $intent,
            'language_tag' => $lang,
            'confidence_hint' => $conf,
            'source' => $source,
        ]);
        $this->stats['training_data']++;
    }

    private function pushQA(string $q, string $a, string $lang, array $meta = []): void
    {
        $baseMeta = ['question' => $q, 'answer' => $a, 'language' => $lang, 'updated_at' => date('c')];
        $this->writeJsonl($this->handles['entities'], [
            'name' => mb_strtolower(trim(preg_replace('/\s+/', ' ', $q) ?? ''), 'UTF-8'),
            'type' => 'qa',
            'language_tag' => $lang,
            'metadata' => array_merge($baseMeta, $meta),
        ]);
        $this->stats['knowledge_entities']++;
    }

    private function pushPattern(string $intent, string $token): void
    {
        $this->writeJsonl($this->handles['patterns'], [
            'intent' => $intent,
            'token' => $token,
            'token_language' => 'en',
            'weight' => 1,
        ]);
        $this->stats['patterns']++;
    }

    private function addPatterns(string $intent, string $text): void
    {
        $text = mb_strtolower($text, 'UTF-8');
        $tokens = preg_split('/\s+/u', $text) ?: [];
        $stopWords = ['a', 'an', 'the', 'is', 'in', 'it', 'of', 'for', 'on', 'with', 'to', 'what', 'when', 'where', 'why', 'how', 'i', 'you', 'he', 'she', 'they', 'we'];

        $tokens = array_values(array_filter($tokens, static function ($t) use ($stopWords): bool {
            if (mb_strlen($t, 'UTF-8') < 3 || mb_strlen($t, 'UTF-8') > 24) {
                return false;
            }
            if (in_array($t, $stopWords, true)) {
                return false;
            }
            return preg_match('/^[\p{L}\p{N}_-]+$/u', $t) === 1;
        }));
        $tokens = array_slice($tokens, 0, 8);
        foreach ($tokens as $token) {
            $this->pushPattern($intent, $token);
        }
    }

    /** @return string[] */
    private function chunkText(string $text, int $chunkSize = 700, int $overlap = 100): array
    {
        if (mb_strlen($text, 'UTF-8') <= $chunkSize) {
            return [$text];
        }

        $chunks = [];
        $offset = 0;
        while ($offset < mb_strlen($text, 'UTF-8')) {
            $chunk = mb_substr($text, $offset, $chunkSize, 'UTF-8');
            if (trim($chunk) !== '') {
                $chunks[] = $chunk;
            }
            $nextOffset = $offset + $chunkSize - $overlap;
            if ($nextOffset <= $offset) { // Prevent infinite loop
                break;
            }
            $offset = $nextOffset;
        }

        return $chunks;
    }
}
