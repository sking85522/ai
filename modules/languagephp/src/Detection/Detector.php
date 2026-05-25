<?php

namespace LanguagePHP\Detection;

class Detector
{
    private static $languageSignatures = [
        'Hindi' => ['range' => '/[\x{0900}-\x{097F}]/u', 'iso' => 'hi'],
        'Marathi' => ['range' => '/[\x{0900}-\x{097F}]/u', 'iso' => 'mr', 'words' => ['आहे', 'नाही', 'काय', 'मी']],
        'Bengali' => ['range' => '/[\x{0980}-\x{09FF}]/u', 'iso' => 'bn'],
        'Gujarati' => ['range' => '/[\x{0A80}-\x{0AFF}]/u', 'iso' => 'gu'],
        'Tamil' => ['range' => '/[\x{0B80}-\x{0BFF}]/u', 'iso' => 'ta'],
        'Telugu' => ['range' => '/[\x{0C00}-\x{0C7F}]/u', 'iso' => 'te'],
        'Kannada' => ['range' => '/[\x{0C80}-\x{0CFF}]/u', 'iso' => 'kn'],
        'Malayalam' => ['range' => '/[\x{0D00}-\x{0D7F}]/u', 'iso' => 'ml'],
        'Punjabi' => ['range' => '/[\x{0A00}-\x{0A7F}]/u', 'iso' => 'pa'],
        'Urdu' => ['range' => '/[\x{0A00}-\x{0A7F}]/u', 'iso' => 'ur', 'words' => ['ہے', 'نہیں', 'کیا']],
        'Arabic' => ['range' => '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}]/u', 'iso' => 'ar'],
        'Chinese' => ['range' => '/[\x{4E00}-\x{9FFF}]/u', 'iso' => 'zh'],
        'Japanese' => ['range' => '/[\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', 'iso' => 'ja'],
        'Korean' => ['range' => '/[\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', 'iso' => 'ko'],
        'Russian' => ['range' => '/[\x{0400}-\x{04FF}]/u', 'iso' => 'ru'],
        'Greek' => ['range' => '/[\x{0370}-\x{03FF}]/u', 'iso' => 'el'],
        'English' => ['iso' => 'en', 'words' => ['the', 'is', 'in', 'and', 'to', 'a', 'of', 'for']],
        'Spanish' => ['iso' => 'es', 'words' => ['de', 'la', 'que', 'el', 'en', 'y', 'a', 'los', 'se']],
        'French' => ['iso' => 'fr', 'words' => ['de', 'la', 'le', 'et', 'les', 'des', 'en', 'un', 'une']],
        'German' => ['iso' => 'de', 'words' => ['der', 'die', 'und', 'in', 'den', 'von', 'zu', 'das', 'mit']],
        'Italian' => ['iso' => 'it', 'words' => ['di', 'e', 'il', 'la', 'che', 'in', 'cui', 'un', 'una']],
        'Portuguese' => ['iso' => 'pt', 'words' => ['de', 'a', 'o', 'que', 'e', 'do', 'da', 'em', 'um']],
    ];

    public static function detect(string $text): array
    {
        $text = mb_strtolower($text, 'UTF-8');
        $bestMatch = 'Unknown';
        $bestIso = 'xx';
        $bestScore = 0;

        $scriptMatches = [];
        foreach (self::$languageSignatures as $lang => $data) {
            if (isset($data['range'])) {
                $count = preg_match_all($data['range'], $text);
                if ($count > 0) {
                    $scriptMatches[$lang] = $count;
                }
            }
        }

        if (!empty($scriptMatches)) {
            arsort($scriptMatches);
            $topScript = array_key_first($scriptMatches);

            $candidates = [];
            foreach (self::$languageSignatures as $lang => $data) {
                if (isset($data['range']) && $data['range'] === self::$languageSignatures[$topScript]['range']) {
                    $candidates[] = $lang;
                }
            }

            if (count($candidates) > 1) {
                preg_match_all('/[\p{L}]+/u', $text, $wordsInTextMatch);
                $wordsInText = $wordsInTextMatch[0];

                $maxWordMatch = -1;
                $resolvedLang = $topScript;

                foreach ($candidates as $candidate) {
                    if (isset(self::$languageSignatures[$candidate]['words'])) {
                        $matchCount = count(array_intersect($wordsInText, self::$languageSignatures[$candidate]['words']));
                        if ($matchCount > $maxWordMatch) {
                            $maxWordMatch = $matchCount;
                            $resolvedLang = $candidate;
                        }
                    }
                }
                $bestMatch = $resolvedLang;
                $bestScore = $scriptMatches[$topScript];
            } else {
                $bestMatch = $topScript;
                $bestScore = $scriptMatches[$topScript];
            }

            $bestIso = self::$languageSignatures[$bestMatch]['iso'];

            return [
                'language' => $bestMatch,
                'iso' => $bestIso,
                'confidence' => min(1.0, $bestScore / max(1, mb_strlen($text, 'UTF-8')))
            ];
        }

        preg_match_all('/[\p{L}]+/u', $text, $wordsInTextMatch);
        $words = $wordsInTextMatch[0];
        $totalWords = count($words);

        if ($totalWords == 0) {
            return ['language' => 'Unknown', 'iso' => 'xx', 'confidence' => 0.0];
        }

        foreach (self::$languageSignatures as $lang => $data) {
            if (isset($data['words'])) {
                $matches = count(array_intersect($words, $data['words']));
                $score = $matches / $totalWords;

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $lang;
                    $bestIso = $data['iso'];
                }
            }
        }

        return [
            'language' => $bestMatch,
            'iso' => $bestIso,
            'confidence' => $bestScore
        ];
    }
}
