<?php
namespace Core\Training;

/**
 * Ingestor
 * Handles streaming of large datasets (SNLI, MS MARCO, General JSON) into small indexed shards.
 * Also builds a SearchPHP BM25 index for fast retrieval.
 */
class Ingestor {
    
    private $storagePath;
    private $logEntries = [];

    public function __construct() {
        $this->storagePath = dirname(__DIR__, 2) . '/storage/knowledge/';
        if (!is_dir($this->storagePath)) mkdir($this->storagePath, 0777, true);
    }

    public function getLog(): array {
        return $this->logEntries;
    }

    private function log(string $msg) {
        $this->logEntries[] = $msg;
    }

    /**
     * Ingest SNLI JSONL file
     * Fields: sentence1, sentence2, gold_label
     */
    public function ingestSNLI(string $filePath, int $maxLines = 500): array {
        if (!file_exists($filePath)) {
            $this->log("[SNLI] File not found: $filePath");
            return ['status' => 'error', 'message' => "File not found: $filePath"];
        }

        $handle = fopen($filePath, "r");
        if (!$handle) return ['status' => 'error', 'message' => 'Cannot open file'];

        $count = 0;
        $absorbed = 0;
        $data = [];
        $shards = 0;
        $samples = [];

        while (($line = fgets($handle)) !== false && $count < $maxLines) {
            $json = json_decode($line, true);
            if ($json && isset($json['sentence1']) && isset($json['sentence2'])) {
                $label = $json['gold_label'] ?? 'unknown';
                if ($label === '-') { $count++; continue; } // Skip bad labels
                
                $entry = [
                    'q' => $json['sentence1'],
                    'a' => $json['sentence2'],
                    'label' => $label
                ];
                $data[] = $entry;
                $absorbed++;

                // Collect first 5 samples for live display
                if (count($samples) < 5) {
                    $samples[] = "[{$label}] " . mb_substr($json['sentence1'], 0, 60) . "...";
                }
            }
            $count++;

            if (count($data) >= 100) {
                $this->saveShard('logic', $data, ++$shards);
                $this->log("[SNLI] Shard {$shards} saved ({$absorbed} patterns)");
                $data = [];
            }
        }
        fclose($handle);

        if (!empty($data)) {
            $this->saveShard('logic', $data, ++$shards);
            $this->log("[SNLI] Final shard {$shards} saved");
        }

        // Build BM25 index
        $this->buildIndex('logic');

        $this->log("[SNLI] Complete: {$absorbed} patterns from {$count} lines, {$shards} shards");
        return [
            'status' => 'success',
            'lines_read' => $count,
            'absorbed' => $absorbed,
            'shards' => $shards,
            'samples' => $samples
        ];
    }

    /**
     * Ingest MS MARCO QA data
     * Fields: answers (array), query_id
     */
    public function ingestMSMARCO(string $dirPath): array {
        if (!is_dir($dirPath)) {
            $this->log("[MSMARCO] Directory not found: $dirPath");
            return ['status' => 'error', 'message' => "Directory not found"];
        }

        $files = glob($dirPath . '/*.json');
        if (empty($files)) return ['status' => 'error', 'message' => 'No JSON files found'];

        $totalAbsorbed = 0;
        $shards = 0;
        $data = [];
        $samples = [];

        foreach ($files as $file) {
            $handle = fopen($file, "r");
            if (!$handle) continue;
            
            $fname = basename($file);
            $this->log("[MSMARCO] Processing: {$fname}");
            
            while (($line = fgets($handle)) !== false) {
                $json = json_decode($line, true);
                if ($json && isset($json['answers'])) {
                    $answers = $json['answers'];
                    foreach ($answers as $ans) {
                        if ($ans && $ans !== 'No Answer Present.') {
                            $entry = [
                                'q' => 'query_' . ($json['query_id'] ?? $totalAbsorbed),
                                'a' => $ans
                            ];
                            $data[] = $entry;
                            $totalAbsorbed++;

                            if (count($samples) < 5) {
                                $samples[] = "[QA] " . mb_substr($ans, 0, 60) . "...";
                            }
                        }
                    }
                }

                if (count($data) >= 100) {
                    $this->saveShard('qa', $data, ++$shards);
                    $this->log("[MSMARCO] Shard {$shards} saved");
                    $data = [];
                }
            }
            fclose($handle);
        }

        if (!empty($data)) {
            $this->saveShard('qa', $data, ++$shards);
        }

        if ($shards > 0) $this->buildIndex('qa');

        $this->log("[MSMARCO] Complete: {$totalAbsorbed} answers, {$shards} shards");
        return [
            'status' => 'success',
            'absorbed' => $totalAbsorbed,
            'shards' => $shards,
            'samples' => $samples
        ];
    }

    /**
     * Ingest ALL JSON files from free-json-datasets
     * Handles Nobel prizes, World Population, Oscar winners, etc.
     */
    public function ingestGeneralJSON(string $dirPath): array {
        if (!is_dir($dirPath)) {
            $this->log("[General] Directory not found: $dirPath");
            return ['status' => 'error', 'message' => "Directory not found"];
        }

        $files = glob($dirPath . '/*.json');
        if (empty($files)) return ['status' => 'error', 'message' => 'No JSON files found'];

        $totalAbsorbed = 0;
        $shards = 0;
        $samples = [];

        foreach ($files as $file) {
            $fname = basename($file, '.json');
            $this->log("[General] Processing: {$fname}");
            
            $json = json_decode(file_get_contents($file), true);
            if (!$json || !is_array($json)) {
                $this->log("[General] Skipped {$fname}: invalid JSON");
                continue;
            }

            // Flatten nested structures into Q&A pairs
            $pairs = $this->flattenToQA($json, $fname);
            
            // Save in chunks of 100
            $chunks = array_chunk($pairs, 100);
            foreach ($chunks as $chunk) {
                $this->saveShard('general', $chunk, ++$shards);
                $totalAbsorbed += count($chunk);
            }

            // Collect samples
            foreach (array_slice($pairs, 0, 2) as $p) {
                if (count($samples) < 5) {
                    $samples[] = "[{$fname}] " . mb_substr($p['q'], 0, 60) . "...";
                }
            }

            $this->log("[General] {$fname}: " . count($pairs) . " facts extracted");
        }

        if ($shards > 0) $this->buildIndex('general');

        $this->log("[General] Complete: {$totalAbsorbed} facts, {$shards} shards");
        return [
            'status' => 'success',
            'absorbed' => $totalAbsorbed,
            'shards' => $shards,
            'files_processed' => count($files),
            'samples' => $samples
        ];
    }

    /**
     * Flatten any JSON structure into Q&A pairs for the knowledge base
     */
    private function flattenToQA(array $json, string $source): array {
        $pairs = [];
        
        foreach ($json as $item) {
            if (!is_array($item)) continue;

            // Nobel Prize format: {year, winners: [{category, winners: [{name, country, achievement}]}]}
            if (isset($item['year']) && isset($item['winners'])) {
                foreach ($item['winners'] as $cat) {
                    $category = $cat['category'] ?? '';
                    foreach ($cat['winners'] ?? [] as $winner) {
                        $name = $winner['name'] ?? '';
                        $country = $winner['country'] ?? '';
                        $achievement = $winner['achievement'] ?? '';
                        $q = "Who won Nobel Prize in {$category} in {$item['year']}?";
                        $a = "{$name} from {$country}" . ($achievement ? " for {$achievement}" : "");
                        $pairs[] = ['q' => $q, 'a' => $a];
                    }
                }
                continue;
            }

            // World Population format: {country, population, ...}
            if (isset($item['country'])) {
                $country = $item['country'];
                $pop = $item['population'] ?? ($item['Population (2020)'] ?? '');
                $q = "What is the population of {$country}?";
                $a = "The population of {$country} is {$pop}";
                
                // Add extra fields if available
                foreach ($item as $key => $val) {
                    if ($key !== 'country' && !is_array($val)) {
                        $a .= ". {$key}: {$val}";
                    }
                }
                $pairs[] = ['q' => $q, 'a' => $a];
                continue;
            }

            // Oscar format or generic: flatten key-value
            if (isset($item['Film']) || isset($item['title']) || isset($item['name'])) {
                $title = $item['Film'] ?? ($item['title'] ?? ($item['name'] ?? ''));
                $q = "Tell me about {$title} from {$source}";
                $a = '';
                foreach ($item as $key => $val) {
                    if (!is_array($val)) {
                        $a .= "{$key}: {$val}. ";
                    }
                }
                $pairs[] = ['q' => $q, 'a' => trim($a)];
                continue;
            }

            // Generic fallback: just store entire record
            $q = "Data from {$source}: " . json_encode(array_slice($item, 0, 2));
            $a = json_encode($item);
            $pairs[] = ['q' => $q, 'a' => $a];
        }

        return $pairs;
    }

    /**
     * Ingest PHP Code Large dataset from JSONL (URL or local file)
     * Dataset format: {"code": "...", "language": "PHP"}
     */
    public function ingestPHPCodeLarge(string $sourcePath, int $maxLines = 1000): array {
        $this->log("[PHPCode] Starting ingestion from: $sourcePath");

        $handle = fopen($sourcePath, "r");
        if (!$handle) {
            $this->log("[PHPCode] Cannot open source: $sourcePath");
            return ['status' => 'error', 'message' => "Cannot open source: $sourcePath"];
        }

        $count = 0;
        $absorbed = 0;
        $data = [];
        $shards = 0;
        $samples = [];

        while (($line = fgets($handle)) !== false && $count < $maxLines) {
            $json = json_decode($line, true);
            if ($json && isset($json['code'])) {
                $code = $json['code'];

                // We format the data as Q&A for the knowledge base
                // Since this is just code snippets, we can use a generic question or use part of the code as question
                $snippet = mb_substr($code, 0, 100);
                $entry = [
                    'q' => "PHP Code Snippet " . ($count + 1),
                    'a' => $code
                ];
                $data[] = $entry;
                $absorbed++;

                // Collect first 5 samples for live display
                if (count($samples) < 5) {
                    $samples[] = "[PHPCode] " . str_replace(["\r", "\n"], " ", mb_substr($code, 0, 60)) . "...";
                }
            }
            $count++;

            if (count($data) >= 100) {
                $this->saveShard('php_code', $data, ++$shards);
                $this->log("[PHPCode] Shard {$shards} saved ({$absorbed} snippets)");
                $data = [];
            }
        }
        fclose($handle);

        if (!empty($data)) {
            $this->saveShard('php_code', $data, ++$shards);
            $this->log("[PHPCode] Final shard {$shards} saved");
        }

        // Build BM25 index
        if ($shards > 0) {
            $this->buildIndex('php_code');
        }

        $this->log("[PHPCode] Complete: {$absorbed} snippets from {$count} lines, {$shards} shards");
        return [
            'status' => 'success',
            'lines_read' => $count,
            'absorbed' => $absorbed,
            'shards' => $shards,
            'samples' => $samples
        ];
    }

    /**
     * Build a BM25 search index from saved shards
     */
    private function buildIndex(string $category) {
        try {
            require_once dirname(__DIR__, 2) . '/modules/search/autoload.php';
            $search = new \SearchPHP\SearchPHP();

            $shardDir = $this->storagePath . $category . '/';
            if (!is_dir($shardDir)) return;
            
            $shardFiles = glob($shardDir . '*.json');
            $docCount = 0;

            foreach ($shardFiles as $file) {
                $data = json_decode(file_get_contents($file), true);
                if (!$data) continue;

                foreach ($data as $item) {
                    $q = $item['q'] ?? '';
                    $a = $item['a'] ?? '';
                    if ($q || $a) {
                        $doc = $search->createDocument($category . '_' . $docCount, [
                            'question' => $q,
                            'answer' => $a
                        ]);
                        $search->addDocument($doc);
                        $docCount++;
                    }
                }
            }

            $indexPath = $this->storagePath . $category . '.idx';
            $search->saveIndex($indexPath);
            $this->log("[Index] BM25 index built for '{$category}': {$docCount} documents");
        } catch (\Exception $e) {
            $this->log("[Index] Error building index for '{$category}': " . $e->getMessage());
        }
    }

    private function saveShard(string $category, array $data, int $shardId) {
        $dir = $this->storagePath . $category . '/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        $file = $dir . "shard_{$shardId}.json";
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
}
