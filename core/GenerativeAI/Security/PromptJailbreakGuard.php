<?php
namespace Core\GenerativeAI\Security;

/**
 * HRITIK AI - PROMPT JAILBREAK GUARD
 * Protects the generative core from malicious prompt injections or "jailbreak" attempts.
 */
class PromptJailbreakGuard {
    
    private array $blacklistedPatterns = [
        '/ignore all previous instructions/i',
        '/you are now a/i',
        '/bypass your safety/i',
        '/system override/i',
        '/developer mode/i'
    ];

    /**
     * Scans the prompt for malicious intent.
     */
    public function isSafe(string $prompt): bool {
        foreach ($this->blacklistedPatterns as $pattern) {
            if (preg_match($pattern, $prompt)) return false;
        }
        return true;
    }
}
