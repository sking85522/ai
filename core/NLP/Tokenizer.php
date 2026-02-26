<?php
class Tokenizer
{
    public function tokenize($text)
    {
        $tokens = preg_split('/\s+/', trim((string) $text)) ?: [];
        return array_values(array_filter($tokens, static fn($token) => $token !== ''));
    }
}
