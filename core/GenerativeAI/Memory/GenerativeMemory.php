<?php
namespace Core\GenerativeAI\Memory;

/**
 * HRITIK AI - GENERATIVE MEMORY (CONTEXT COMPRESSOR)
 * Summarizes long conversation histories into small, dense neural tokens for the generator.
 */
class GenerativeMemory {
    
    private array $compressedContext = [];

    /**
     * Compresses multiple chat lines into a single summary string.
     */
    public function compress(array $history): string {
        if (count($history) < 2) return implode(" ", $history);

        // Simple Heuristic Summary (In a real LLM, this would be a T5/BART model)
        $summary = "User was talking about: " . substr($history[0], 0, 50) . "... ";
        $summary .= "Last interaction was: " . end($history);

        return $summary;
    }

    public function store(string $summary): void {
        $this->compressedContext[] = $summary;
    }

    public function getLatestSummary(): string {
        return end($this->compressedContext) ?: "";
    }
}
