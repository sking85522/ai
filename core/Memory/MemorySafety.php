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

    public function canStore(string $key, string $value): bool
    {
        $x = mb_strtolower($key . ' ' . $value, 'UTF-8');
        $blocked = [
            'password', 'passwd', 'otp', 'cvv', 'card number',
            'atm pin', 'upi pin', 'bank account', 'ssn', 'aadhaar'
        ];
        foreach ($blocked as $word) {
            if (str_contains($x, $word)) {
                return false;
            }
        }
        return true;
    }

    public function safeValue(string $value): string
    {
        $value = trim($value);
        if (strlen($value) <= 4) {
            return $value;
        }
        return substr($value, 0, 2) . str_repeat('*', max(0, strlen($value) - 4)) . substr($value, -2);
    }
}
