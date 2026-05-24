<?php
namespace Core\Response;

/**
 * HRITIK AI - NEURAL SYNTHESIZER
 * Converts raw database/online results into clean, pure responses.
 * NO default prefixes or personalities.
 */
class NeuralSynthesizer {
    
    public function synthesize(string $prompt, ?string $rawResponse, string $source): string {
        if (empty($rawResponse) || str_contains($rawResponse, "not learned enough")) {
            // Even the fallback must be minimal or fetched from memory.
            return "Seeking further knowledge..."; 
        }

        // 1. CLEANING: Remove robots/math jargon from SQuAD/SNLI
        $clean = $this->cleanJargon($rawResponse);

        // 2. CODING: If it's code, wrap it properly (Structural, not conversational)
        if ($this->isCode($clean)) {
            return "```python\n" . trim($clean) . "\n```";
        }

        return $clean;
    }

    private function cleanJargon(string $text): string {
        $jargon = [
            'Fact check:', 'entailment', 'neutral', 'contradiction', 'answer:', 'context:',
            '( ( ', ' ) )', ' . . '
        ];
        $text = str_replace($jargon, '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function isCode(string $text): bool {
        return str_contains($text, 'def ') || str_contains($text, 'import ') || 
               str_contains($text, '<?php') || str_contains($text, 'print(');
    }
}
