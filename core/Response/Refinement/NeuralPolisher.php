<?php
namespace Core\Response\Refinement;

/**
 * HRITIK AI - NEURAL POLISHER
 * Scans and refines the generated response using the original online database bridge.
 */
class NeuralPolisher {
    
    private static array $refinementCache = [];

    /**
     * Polishes the response text using patterns from the Online DB.
     */
    public function polish(string $text): string {
        $patterns = $this->getRefinementPatterns();
        
        foreach ($patterns as $robotic => $natural) {
            // Safety Check: Skip if the replacement looks like junk or placeholders
            if (stripos($natural, 'Prefix') !== false || stripos($natural, 'arre') !== false) continue;
            
            // Only replace if the robotic word is at least 3 chars to avoid common word poisoning
            if (strlen($robotic) < 3) continue;

            $text = preg_replace('/\b' . preg_quote($robotic, '/') . '\b/i', $natural, $text);
        }
        
        return ucfirst(trim($text));
    }

    /**
     * Fetches refinement patterns from Online DB.
     */
    private function getRefinementPatterns(): array {
        if (!empty(self::$refinementCache)) return self::$refinementCache;

        require_once __DIR__ . '/../../../online_db.php';
        global $db;
        
        if (!isset($db) || $db === null) return [];

        $results = $db->query("SELECT k_key, k_value FROM neural_knowledge WHERE category = 'refinement'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                self::$refinementCache[$row['k_key']] = $row['k_value'];
            }
        }

        return self::$refinementCache;
    }
}
