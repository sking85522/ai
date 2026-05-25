<?php
namespace Core\NLP\Translation;

/**
 * HRITIK AI - TRANSLITERATION ENGINE (ONLINE DB EDITION)
 * Converts between Devanagari and Roman characters using the remote neural database.
 */
class TransliterationEngine {
    
    private static array $scriptCache = [];

    /**
     * Converts Devanagari text to Roman Hinglish using Online DB maps.
     */
    public function toHinglish(string $text): string {
        $map = $this->getMap();
        return strtr($text, $map);
    }

    /**
     * Fetches transliteration map from Online DB.
     */
    private function getMap(): array {
        if (!empty(self::$scriptCache)) return self::$scriptCache;

        require_once __DIR__ . '/../../../online_db.php';
        global $db;
        
        $results = $db->query("SELECT k_key, k_value FROM neural_knowledge WHERE category = 'transliteration'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                self::$scriptCache[$row['k_key']] = $row['k_value'];
            }
        }

        return self::$scriptCache;
    }
}
