<?php
class IntentNormalizer
{
    public function normalize(string $intent): string
    {
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
