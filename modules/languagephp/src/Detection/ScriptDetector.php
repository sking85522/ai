<?php
namespace LanguagePHP\Detection;

/**
 * Script detector — Identifies writing system used in text.
 * Detects Latin, Cyrillic, Arabic, Devanagari, CJK, Bengali, Tamil, etc.
 */
class ScriptDetector
{
    private static $scripts = [
        'Latin' => '/[\x{0041}-\x{024F}]/u',
        'Cyrillic' => '/[\x{0400}-\x{04FF}]/u',
        'Arabic' => '/[\x{0600}-\x{06FF}]/u',
        'Devanagari' => '/[\x{0900}-\x{097F}]/u',
        'Bengali' => '/[\x{0980}-\x{09FF}]/u',
        'Gurmukhi' => '/[\x{0A00}-\x{0A7F}]/u',
        'Gujarati' => '/[\x{0A80}-\x{0AFF}]/u',
        'Tamil' => '/[\x{0B80}-\x{0BFF}]/u',
        'Telugu' => '/[\x{0C00}-\x{0C7F}]/u',
        'Kannada' => '/[\x{0C80}-\x{0CFF}]/u',
        'Malayalam' => '/[\x{0D00}-\x{0D7F}]/u',
        'Thai' => '/[\x{0E00}-\x{0E7F}]/u',
        'Georgian' => '/[\x{10A0}-\x{10FF}]/u',
        'Hangul' => '/[\x{AC00}-\x{D7AF}]/u',
        'Hiragana' => '/[\x{3040}-\x{309F}]/u',
        'Katakana' => '/[\x{30A0}-\x{30FF}]/u',
        'CJK' => '/[\x{4E00}-\x{9FFF}]/u',
        'Greek' => '/[\x{0370}-\x{03FF}]/u',
        'Hebrew' => '/[\x{0590}-\x{05FF}]/u',
    ];

    /**
     * Detect script used in text.
     * @return array ['primary' => string, 'scripts' => ['Script' => count], 'confidence' => float]
     */
    public static function detect(string $text): array
    {
        $counts = [];
        $totalChars = 0;

        foreach (self::$scripts as $scriptName => $pattern) {
            preg_match_all($pattern, $text, $matches);
            $count = count($matches[0]);
            if ($count > 0) {
                $counts[$scriptName] = $count;
                $totalChars += $count;
            }
        }

        if (empty($counts)) {
            return ['primary' => 'Unknown', 'scripts' => [], 'confidence' => 0.0];
        }

        arsort($counts);
        $primary = array_key_first($counts);
        $confidence = $counts[$primary] / max(1, $totalChars);

        return [
            'primary' => $primary,
            'scripts' => $counts,
            'confidence' => round($confidence, 4),
        ];
    }
}
