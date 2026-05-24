<?php
namespace Core\Virtual;

/**
 * HRITIK AI - ADVANCED REFLECTION ENGINE
 * Enables the AI to critique, polish, and de-robotize its own responses.
 */
class ReflectionEngine {
    
    /**
     * Critiques and improves the response before final output.
     */
    public function reflect(string $prompt, string $response): string {
        // 1. DE-ROBOTIZE: Strip robotic jargon (SQuAD/SNLI leftovers)
        $clean = $this->deRobotize($response);

        // 2. IDENTITY CHECK: Ensure the AI knows it's Hritik AI
        if ($this->isAskingIdentity($prompt)) {
            return "Main Hritik AI hoon, jise Sachin (Hritik Softwares) ne develop kiya hai. Mere paas 53 Lakh se zyada records ka neural database hai.";
        }

        // 3. COHERENCE POLISH: Fix broken sentence endings
        if (!preg_match('/[\.\?\!]$/u', $clean)) $clean .= ".";

        return $clean;
    }

    private function deRobotize(string $text): string {
        $jargon = ['Fact check:', 'entailment', 'neutral', 'contradiction', 'answer:', 'context:'];
        $text = str_ireplace($jargon, '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function isAskingIdentity(string $text): bool {
        return preg_match('/(kaun ho|tera naam|who are you|your name|kisne banaya)/ui', $text);
    }
}
