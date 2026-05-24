<?php
namespace Core\GenerativeAI\Style;

/**
 * HRITIK AI - STYLISTIC GRAFTING ENGINE (ONLINE DB EDITION)
 * Injects literary styles using data from the remote neural database.
 */
class StylisticGrafting {
    
    private static array $styleCache = [];

    /**
     * Grafts a style onto the response using Online DB data.
     */
    public function apply(string $text, string $styleName = 'poetic'): string {
        $styles = $this->getStyles();
        $style = $styles[$styleName] ?? null;
        if (!$style) return $text;

        return ($style['prefix'] ?? '') . lcfirst($text) . ($style['suffix'] ?? '');
    }

    /**
     * Fetches styles from Online DB.
     */
    private function getStyles(): array {
        if (!empty(self::$styleCache)) return self::$styleCache;

        require_once __DIR__ . '/../../../online_db.php';
        global $db;
        
        $results = $db->query("SELECT sub_category, k_key, k_value FROM neural_knowledge WHERE category = 'style'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                $style = $row['sub_category'];
                $prop = $row['k_key']; // prefix or suffix
                $val = $row['k_value'];
                
                if (!isset(self::$styleCache[$style])) self::$styleCache[$style] = [];
                self::$styleCache[$style][$prop] = $val;
            }
        }

        return self::$styleCache;
    }
}
