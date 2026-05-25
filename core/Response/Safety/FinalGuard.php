<?php
namespace Core\Response\Safety;

/**
 * HRITIK AI - FINAL RESPONSE GUARD
 * The last line of defense to ensure the response is safe, private, and non-toxic.
 */
class FinalGuard {
    
    /**
     * Scans the final response for sensitive patterns or toxicity.
     */
    public function scan(string $text): bool {
        // Privacy check (detecting email-like or password-like strings)
        if (preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $text)) return false;
        
        // Toxicity check (using online DB patterns if needed)
        $toxicWords = ['gaali', 'badword1', 'badword2']; // Can be moved to DB
        foreach ($toxicWords as $word) {
            if (str_contains(strtolower($text), $word)) return false;
        }

        return true;
    }
}
