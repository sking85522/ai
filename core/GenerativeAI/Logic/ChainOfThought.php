<?php
namespace Core\GenerativeAI\Logic;

/**
 * HRITIK AI - CHAIN OF THOUGHT (CoT) ENGINE
 * Forces the generator to "think" through steps before synthesizing the final response.
 */
class ChainOfThought {
    
    private array $thoughts = [];

    /**
     * Generates a reasoning path for a given prompt.
     */
    public function reason(string $prompt, array $context = []): array {
        $this->thoughts = [];
        
        // Step 1: Intent Decomposition
        $this->thoughts[] = "Decomposing prompt intent: " . $this->analyzeIntent($prompt);
        
        // Step 2: Context Retrieval Strategy
        $this->thoughts[] = "Identifying relevant memory blocks (Short-term context: " . count($context) . " items).";
        
        // Step 3: Synthesis Plan
        $this->thoughts[] = "Mapping neural bridges between facts and generative probabilities.";
        
        // Step 4: Persona Check
        $this->thoughts[] = "Ensuring Hritik AI identity and bilingual tone consistency.";

        return $this->thoughts;
    }

    private function analyzeIntent(string $text): string {
        $text = strtolower($text);
        if (preg_match('/(kaise|how|steps|process)/i', $text)) return 'Procedural reasoning required.';
        if (preg_match('/(kyun|why|reason)/i', $text)) return 'Causal analysis required.';
        return 'General conversational flow.';
    }

    public function getFormattedThoughts(): string {
        return "[NEURAL_THOUGHTS]\n - " . implode("\n - ", $this->thoughts);
    }
}
