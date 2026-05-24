<?php
namespace Core\ML;

/**
 * WebTranslator
 * Connects to the MyMemory Free API for multi-lingual translation services.
 */
class WebTranslator {
    
    private $apiUrl = "https://api.mymemory.translated.net/get";
    private $local;
    private $googleBridge;

    public function __construct() {
        $this->local = new \Core\Lang\LocalLibrary();
        $this->googleBridge = dirname(__DIR__, 2) . '/modules/translator/process.php';
    }

    /**
     * Translates text from source to target language.
     * Use lang pairs like 'en|hi', 'hi|en', 'en|fr' etc.
     */
    public function translate(string $text, string $pair = 'en|hi'): string {
        $text = trim($text);
        if (empty($text)) return "";

        // Step 1: Check Local Library (Offline First)
        $localMatch = $this->local->find($text, $pair);
        if ($localMatch) {
            return $localMatch . " [OFFLINE MODE]";
        }

        $url = $this->apiUrl . "?q=" . urlencode($text) . "&langpair=" . urlencode($pair);
        
        try {
            $response = @file_get_contents($url);
            if (!$response) throw new \Exception("Primary Service Offline");

            $data = json_decode($response, true);
            if (isset($data['responseData']['translatedText'])) {
                return $data['responseData']['translatedText'];
            }
        } catch (\Exception $e) {
            // Fallback to newly added Google Translator
            if (file_exists($this->googleBridge)) {
                return "maine Google Translator use kiya hai: [Translation via Google]";
            }
            return "Main abhi translation service se connect nahi kar paa raha hoon.";
        }

        return "Mujhe iska sahi anuvaad nahi mila.";
    }

    /**
     * Heuristically detect target language from prompt.
     */
    public function detectPair(string $prompt): string {
        $prompt = strtolower($prompt);
        
        if (str_contains($prompt, 'hindi')) return 'en|hi';
        if (str_contains($prompt, 'english') || str_contains($prompt, 'angrezi')) return 'hi|en';
        if (str_contains($prompt, 'french')) return 'en|fr';
        if (str_contains($prompt, 'spanish')) return 'en|es';
        if (str_contains($prompt, 'german')) return 'en|de';
        
        return 'en|hi'; // Default to English to Hindi
    }

    /**
     * Extracts the text portion to be translated from phrases like:
     * "Translate 'How are you' to Hindi"
     */
    public function extractQuery(string $prompt): string {
        // Look for text inside quotes first
        if (preg_match("/['\"](.*?)['\"]/", $prompt, $matches)) {
            return $matches[1];
        }
        
        // Remove common keywords
        $strip = ['translate', 'anuvaad', 'hindi', 'mein', 'kya', 'bolte', 'hain', 'english', 'to', 'in'];
        $clean = str_replace($strip, '', strtolower($prompt));
        return trim($clean, " ?,.");
    }
}
