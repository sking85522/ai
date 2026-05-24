<?php
namespace Core\Memory;

class MemorySafety {
    
    private array $blacklist = ['password', 'secret', 'key', 'token', 'credit card'];

    /**
     * Sanitizes a string before storage.
     */
    public function sanitize(string $content): string {
        foreach ($this->blacklist as $term) {
            $content = str_ireplace($term, '[REDACTED]', $content);
        }
        return $content;
    }

    /**
     * Check if the content is safe to store.
     */
    public function isSafe(string $content): bool {
        // More complex logic can be added here
        return true; 
    }
}
