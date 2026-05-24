<?php
namespace Core\NLP\Logic;

/**
 * HRITIK AI - COREFERENCE RESOLVER (ONLINE DB EDITION)
 * Resolves pronouns using a dynamic list stored in the remote neural database.
 */
class CoreferenceResolver {
    
    private static array $pronounCache = [];

    /**
     * Resolves pronouns using Online DB data.
     */
    public function resolve(string $text, ?string $lastSubject): string {
        if (!$lastSubject) return $text;

        $pronouns = $this->getPronouns();
        foreach ($pronouns as $pronoun) {
            $text = preg_replace('/\b' . $pronoun . '\b/i', $lastSubject, $text);
        }
        
        return $text;
    }

    /**
     * Fetches pronouns from Online DB.
     */
    private function getPronouns(): array {
        if (!empty(self::$pronounCache)) return self::$pronounCache;

        require_once __DIR__ . '/../../../online_db.php';
        global $db;
        
        $results = $db->query("SELECT k_key FROM neural_knowledge WHERE category = 'pronoun'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                self::$pronounCache[] = $row['k_key'];
            }
        }

        return self::$pronounCache;
    }
}
