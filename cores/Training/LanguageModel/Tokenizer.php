<?php
namespace Core\Training\LanguageModel;

class Tokenizer {
    public function tokenize(string $text): array {
        $text = strtolower($text);
        $text = preg_replace('/([?.!,;:\(\)\[\]\{\}])/u', ' $1 ', $text);
        $parts = preg_split('/\s+/u', trim($text));
        return array_values(array_filter($parts ?: [], fn($token) => $token !== ''));
    }
}
