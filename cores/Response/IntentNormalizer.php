<?php
namespace Core\Response;

class IntentNormalizer {
    private array $mapping = [
        'hi' => 'greeting',
        'hello' => 'greeting',
        'hey' => 'greeting',
        'namaste' => 'greeting',
        'who are you' => 'identity',
        'your name' => 'identity',
        'naam kya hai' => 'identity',
        'maths' => 'math',
        'calculate' => 'math',
        'solve' => 'math'
    ];

    /**
     * Normalizes a predicted intent or raw text into a standard key.
     */
    public function normalize(string $rawIntent): string {
        $rawIntent = strtolower(trim($rawIntent));
        return $this->mapping[$rawIntent] ?? $rawIntent;
    }
}
