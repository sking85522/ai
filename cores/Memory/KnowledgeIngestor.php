<?php
namespace Core\Memory;

require_once __DIR__ . '/../ML/SupervisedLearner.php';
use Core\ML\SupervisedLearner;

class KnowledgeIngestor {
    private SupervisedLearner $learner;
    private array $supportedExtensions = ['txt', 'md', 'json', 'php', 'js'];

    public function __construct() {
        $this->learner = new SupervisedLearner();
    }

    /**
     * Scans a directory and learns from its contents.
     */
    public function ingestDirectory(string $dirPath): array {
        if (!is_dir($dirPath)) {
            return ['status' => 'error', 'message' => "Directory not found: $dirPath"];
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath));
        $count = 0;
        $learnedFacts = 0;

        foreach ($files as $file) {
            if ($file->isDir()) continue;
            
            $ext = strtolower($file->getExtension());
            if (in_array($ext, $this->supportedExtensions)) {
                $content = file_get_contents($file->getRealPath());
                if (empty($content)) continue;

                $facts = $this->extractFacts($file->getBasename(), $content);
                foreach ($facts as $q => $a) {
                    $this->learner->teach($q, $a);
                    $learnedFacts++;
                }
                $count++;
            }
        }

        return [
            'status' => 'success',
            'files_processed' => $count,
            'facts_learned' => $learnedFacts
        ];
    }

    /**
     * Heuristic to extract QA pairs from raw text.
     */
    private function extractFacts(string $filename, string $content): array {
        $facts = [];
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        // Fact 1: File identity
        $facts["What is in the file $filename?"] = "The file $filename contains: " . substr(strip_tags($content), 0, 200) . "...";
        $facts["$basename file details"] = "Content from $filename: " . substr(strip_tags($content), 0, 500);

        // Fact 2: Extract Definitions (e.g. "Term is Definition")
        // Refined regex: Allows smaller terms (2 chars) and better capturing
        if (preg_match_all('/([A-Z0-9][a-zA-Z0-9\s]{1,30})\s+(is|means|refers to|represents)\s+([^.\n]+)/i', $content, $matches)) {
            foreach ($matches[1] as $i => $term) {
                $q = trim($term);
                $verb = $matches[2][$i];
                $definition = trim($matches[3][$i]);
                
                $facts["What is $q?"] = "$q $verb $definition.";
                $facts["$q meaning"] = "$q $verb $definition.";
            }
        }

        // Fact 3: Extract Key-Value pairs from JSON
        if (str_ends_with($filename, '.json')) {
            $json = json_decode($content, true);
            if (is_array($json)) {
                foreach ($json as $key => $val) {
                    if (is_string($val) && strlen($val) < 200) {
                        $facts["What is the value of $key?"] = "The value of $key is $val.";
                    }
                }
            }
        }

        return $facts;
    }
}
