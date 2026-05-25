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
        $intent = $rawIntent
        $rawIntent = strtolower(trim($rawIntent));
        return $this->mapping[$rawIntent] ?? $rawIntent;
        
        $intent = strtolower(trim($intent));
        if ($intent === '') {
            return 'general';
        }

        if (str_starts_with($intent, 'nli_')) {
            return 'normal_chat';
        }

        $map = [
            'unknown' => 'general',
            'code_request' => 'code_generate',
            'coding_help' => 'code_generate',
            'engineering_request' => 'engineering_plan',
            'debug_request' => 'debug_help',
            'news_topic' => 'normal_chat',
            'quiz' => 'quiz_request',
        ];

        return $map[$intent] ?? $intent;
    }
}