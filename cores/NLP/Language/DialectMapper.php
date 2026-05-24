<?php
namespace Core\NLP\Language;

/**
 * HRITIK AI - REGIONAL DIALECT MAPPER (ONLINE DB EDITION)
 * Maps regional slangs and dialects using data from the remote neural database.
 */
class DialectMapper {
    
    private static array $cache = [];

    /**
     * Maps dialect words to standard concepts using Online Database.
     */
    public function map(string $text): string {
        $dialects = $this->getDialects();
        
        foreach ($dialects as $slang => $standard) {
            $text = str_ireplace($slang, $standard, $text);
        }
        
        return $text;
    }

    /**
     * Fetches dialects from Online DB with caching.
     */
    private function getDialects(): array {
        if (!empty(self::$cache)) return self::$cache;

        require_once __DIR__ . '/../../../online_db.php';
        $db = \RemoteDB::getInstance();
        
        $results = $db->query("SELECT k_key, k_value FROM neural_knowledge WHERE category = 'dialect'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                self::$cache[$row['k_key']] = $row['k_value'];
            }
        }

        return self::$cache;
    }
}
