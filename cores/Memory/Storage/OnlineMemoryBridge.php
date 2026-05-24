<?php
namespace Core\Memory\Storage;

/**
 * HRITIK AI - CLOUD NEURAL MEMORY BRIDGE
 * 
 * Manages semantic and episodic memory persistence using the online database.
 * Local storage has been disabled as per user request.
 */
class OnlineMemoryBridge {
    
    public function __construct() {
        // Local storage initialization removed as per request
    }

    /**
     * Persists memory data to the Cloud Neural DB.
     */
    public function save(string $category, string $key, $value): bool {
        global $db;

        $encoded = is_array($value) ? json_encode($value) : $value;
        
        if (isset($db) && $db !== null) {
            // Use REPLACE INTO to update existing keys or insert new ones
            $sql = "REPLACE INTO neural_memory (m_key, m_value, category) 
                    VALUES ('$key', '" . addslashes($encoded) . "', '$category')";
            $res = $db->query($sql);
            return isset($res['status']) && $res['status'] === 'success';
        }

        return false;
    }

    /**
     * Retrieves memory data from the Cloud Neural DB.
     */
    public function get(string $category, string $key): ?string {
        global $db;
        if (isset($db) && $db !== null) {
            $sql = "SELECT m_value FROM neural_memory WHERE m_key = '$key' AND category = '$category' LIMIT 1";
            $res = $db->query($sql);
            
            if (isset($res['data'][0]['m_value'])) {
                return $res['data'][0]['m_value'];
            }
        }

        return null;
    }
}
