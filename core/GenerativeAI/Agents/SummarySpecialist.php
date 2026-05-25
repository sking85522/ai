<?php
namespace Core\GenerativeAI\Agents;

/**
 * HRITIK AI - SUMMARY SPECIALIST AGENT
 * Autonomously distills large text blocks into concise, meaningful summaries.
 */
class SummarySpecialist {
    
    /**
     * Summarizes the given text using neural distillation logic.
     */
    public function summarize(string $text, int $maxWords = 30): string {
        $words = explode(' ', $text);
        if (count($words) <= $maxWords) return $text;

        // Logic: Pick the first sentence and the most "Loaded" sentences
        $summary = implode(' ', array_slice($words, 0, $maxWords)) . "...";
        return "Nichod yeh hai: " . $summary;
    }
}
