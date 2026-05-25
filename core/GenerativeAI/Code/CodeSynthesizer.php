<?php
namespace Core\GenerativeAI\Code;

/**
 * HRITIK AI - NEURAL CODE SYNTHESIZER
 * Generates and formats code snippets within the generative output.
 */
class CodeSynthesizer {
    
    /**
     * Formats a raw logic block into a clean code snippet.
     */
    public function synthesize(string $logic, string $lang = 'php'): string {
        $snippet = "```$lang\n";
        $snippet .= $logic;
        $snippet .= "\n```";
        return $snippet;
    }

    /**
     * Detects if the prompt is asking for code.
     */
    public function isCodeRequest(string $prompt): bool {
        return preg_match('/(code|function|class|script|program|likho)/i', $prompt);
    }
}
