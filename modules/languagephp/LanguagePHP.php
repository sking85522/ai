<?php

namespace LanguagePHP;

use LanguagePHP\Detection\Detector;
use LanguagePHP\Detection\ScriptDetector;

class LanguagePHP
{
    /**
     * Detect language of text using n-gram frequency analysis.
     * @return array ['language' => string, 'confidence' => float, 'scores' => [...]]
     */
    public static function detect(string $text): array
    {
        return Detector::detect($text);
    }

    /**
     * Detect the writing script (Latin, Cyrillic, Arabic, Devanagari, CJK, etc.)
     */
    public static function detectScript(string $text): array
    {
        return ScriptDetector::detect($text);
    }

    /**
     * Check if text is likely in a specific language.
     */
    public static function isLanguage(string $text, string $langCode): bool
    {
        $result = Detector::detect($text);
        return ($result['language'] ?? '') === $langCode;
    }

    /**
     * Get list of supported languages.
     */
    public static function supportedLanguages(): array
    {
        return Detector::getSupportedLanguages();
    }
}
