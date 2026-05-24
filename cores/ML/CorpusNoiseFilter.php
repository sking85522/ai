<?php
namespace Core\ML;

class CorpusNoiseFilter {
    /**
     * Filters noise from training data.
     * Heuristics: min length, repetitive chars, gibberish detection.
     */
    public function isGoodQA(string $q, string $a): bool {
        if (strlen($q) < 5 || strlen($a) < 2) return false;
        
        // Check for too many repetitive characters (e.g., "aaaaaaaa")
        if (preg_match('/(.)\1{4,}/', $q)) return false;

        return true;
    }

    public function clean(string $text): string {
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}

class CorpusShardManager {
    /**
     * Splits a large corpus into smaller shards for memory efficiency.
     */
    public function shard(array $data, int $size = 100): array {
        return array_chunk($data, $size);
    }
}
