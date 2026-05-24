<?php
namespace Core\SQLGenerator;

/**
 * HRITIK AI - DYNAMIC SQL GENERATOR
 * Converts natural language intents into secure, optimized SQL queries.
 */
class SQLGenerator {
    
    /**
     * Generates a SQL query based on the detected intent and parameters.
     */
    public function generate(string $intent, array $params = []): string {
        switch ($intent) {
            case 'search_knowledge':
                return $this->buildKnowledgeQuery($params);
            case 'get_memory':
                return $this->buildMemoryQuery($params);
            case 'save_history':
                return $this->buildHistoryInsert($params);
            case 'search_neurons':
                return $this->buildNeuronQuery($params);
            default:
                return "";
        }
    }

    private function buildKnowledgeQuery(array $params): string {
        $query = trim($params['query'] ?? '');
        if (empty($query)) return "SELECT 1 WHERE 1=0";
        
        $words = explode(' ', strtolower($query));
        $conditions = [];
        foreach ($words as $word) {
            if (strlen($word) < 2) continue;
            $word = addslashes($word);
            $conditions[] = "(k_key LIKE '%$word%' OR tags_json LIKE '%$word%')";
        }
        
        $where = !empty($conditions) ? implode(' OR ', $conditions) : "(k_key LIKE '%" . addslashes($query) . "%')";
        
        return "SELECT k_key, k_value, category FROM neural_knowledge " .
               "WHERE $where AND category != 'intent' AND category != 'system_models' " .
               "ORDER BY (CASE WHEN k_key LIKE '%" . addslashes($query) . "%' THEN 0 ELSE 1 END) ASC LIMIT 5";
    }

    private function buildMemoryQuery(array $params): string {
        $prompt = addslashes($params['prompt'] ?? '');
        return "SELECT response FROM neural_memory " .
               "WHERE prompt LIKE '%$prompt%' " .
               "ORDER BY id DESC LIMIT 1";
    }

    private function buildHistoryInsert(array $params): string {
        $sid = addslashes($params['session_id'] ?? 'default');
        $p = addslashes($params['prompt'] ?? '');
        $r = addslashes($params['response'] ?? '');
        $i = addslashes($params['intent'] ?? 'general');
        return "INSERT INTO neural_history (session_id, prompt, response, intent) " .
               "VALUES ('$sid', '$p', '$r', '$i')";
    }

    private function buildNeuronQuery(array $params): string {
        $keywords = $params['keywords'] ?? [];
        if (empty($keywords)) return "";
        $escaped = array_map(fn($k) => "'" . addslashes($k) . "'", $keywords);
        return "SELECT id, content FROM neurons WHERE content IN (" . implode(',', $escaped) . ")";
    }
}
