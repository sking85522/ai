<?php
namespace Core\NLP;

/**
 * HRITIK AI - MULTILINGUAL LANGUAGE DETECTOR
 * Support for Hindi (Devanagari), English, Arabic, and Hinglish.
 */
class LanguageDetector {
    
    // Hash map for O(1) lookups instead of in_array
    private array $hiMarkers = [
        'hai' => true, 'tha' => true, 'thi' => true, 'the' => true, 'ka' => true,
        'ki' => true, 'main' => true, 'nahi' => true, 'nhi' => true, 'kyun' => true,
        'kya' => true, 'karo' => true, 'kar' => true, 'raha' => true, 'rahi' => true,
        'kuch' => true, 'bhi' => true, 'se' => true, 'ya' => true, 'par' => true,
        'per' => true, 'kaise' => true, 'btao' => true
    ];

    /**
     * Detect language using Unicode Scripts and common markers.
     */
    public function detect(string $text): string {
        $text = trim($text);
        if (empty($text)) return 'en';

        // 1. Script Analysis (Devanagari)
        if (preg_match('/\p{Devanagari}/u', $text)) {
            return 'hi_native';
        }

        // 2. Script Analysis (Arabic/Urdu)
        if (preg_match('/\p{Arabic}/u', $text)) {
            return 'ur';
        }

        // 3. Keyword Analysis (Hinglish/Latin Hindi)
        $words = explode(' ', strtolower($text));
        $hiCount = 0;
        foreach ($words as $word) {
            if (isset($this->hiMarkers[$word])) $hiCount++;
        }

        if ($hiCount >= 1) return 'hi_latin';

        // Default to English
        return 'en';
    }

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