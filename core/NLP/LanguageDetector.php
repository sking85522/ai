<?php
class LanguageDetector
{
    public function detect(string $text): string
    {
        $hasDevanagari = preg_match('/\p{Devanagari}/u', $text) === 1;
        $hasLatin = preg_match('/[A-Za-z]/', $text) === 1;

        if ($hasDevanagari && $hasLatin) {
            return 'bilingual';
        }
        if ($hasDevanagari) {
            return 'hi';
        }
        if ($hasLatin) {
            return 'en';
        }
        return 'unknown';
    }

    public function preferredResponseLanguage(string $inputLanguage): string
    {
        if (in_array($inputLanguage, ['en', 'hi', 'bilingual'], true)) {
            return $inputLanguage;
        }
        return 'bilingual';
    }
}
