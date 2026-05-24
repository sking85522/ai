<?php
namespace Core\Engine;

/**
 * HRITIK AI - NEURAL REASONING ENGINE
 * Uses synaptic weights and concept traversal for task-oriented reasoning.
 */
class NeuralReasoning {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../../online_db.php';
        $this->db = new \RemoteDB();
    }

    public function solve(string $prompt): ?string {
        $keywords = $this->extractKeywords($prompt);
        if (empty($keywords)) return null;

        // 1. Identify Activated Neurons
        $neuronIds = $this->getNeuronIds($keywords);
        if (empty($neuronIds)) return null;

        // 2. Expand via Synapses (Concept Traversal)
        $expandedNeurons = $this->expandNeurons($neuronIds);
        
        // 3. Find Best Memory Match
        return $this->findBestMatch($expandedNeurons);
    }

    private function getNeuronIds(array $keywords): array {
        $escaped = array_map(fn($k) => "'" . addslashes($k) . "'", $keywords);
        $res = $this->db->query("SELECT id FROM neurons WHERE content IN (" . implode(',', $escaped) . ")");
        $ids = [];
        if (isset($res['data'])) {
            foreach ($res['data'] as $row) $ids[] = $row['id'];
        }
        return $ids;
    }

    private function expandNeurons(array $ids): array {
        $idList = implode(',', $ids);
        // Find neurons connected to our active neurons with weight > threshold
        $sql = "SELECT to_id, weight FROM synapses WHERE from_id IN ($idList) AND weight > 0.1 
                UNION 
                SELECT from_id as to_id, weight FROM synapses WHERE to_id IN ($idList) AND weight > 0.1
                ORDER BY weight DESC LIMIT 20";
        
        $res = $this->db->query($sql);
        $expanded = array_fill_keys($ids, 1.0); // Start with original neurons (max weight)
        if (isset($res['data'])) {
            foreach ($res['data'] as $row) {
                $expanded[$row['to_id']] = ($expanded[$row['to_id']] ?? 0) + $row['weight'];
            }
        }
        return $expanded;
    }

    private function findBestMatch(array $neuronScores): ?string {
        if (empty($neuronScores)) {
            return $this->directFallback();
        }
        
        arsort($neuronScores);
        $topNeurons = array_slice(array_keys($neuronScores), 0, 10);
        $neuronList = implode(',', $topNeurons);

        // Find memory records linked to these neurons
        // Prioritize records where the neuron is linked to the PROMPT for better relevance
        $sql = "SELECT m.response, COUNT(t.neuron_id) as relevance 
                FROM neural_memory m
                JOIN memory_trace t ON m.id = t.memory_id
                WHERE t.neuron_id IN ($neuronList)
                GROUP BY m.id
                ORDER BY relevance DESC, m.id DESC
                LIMIT 5"; // Fetch top 5 to filter
        
        $res = $this->db->query($sql);
        if (isset($res['data'])) {
            foreach ($res['data'] as $row) {
                if (!$this->isLikelyMath($row['response'])) return $row['response'];
            }
        }
        return $this->directFallback();
    }

    private function directFallback(): ?string {
        if (empty($this->keywords)) return null;
        
        // Build a query that scores records based on keyword matches
        $scoreConditions = [];
        foreach ($this->keywords as $kw) {
            $kwSafe = addslashes($kw);
            $scoreConditions[] = "(CASE WHEN prompt LIKE '%$kwSafe%' THEN 5 ELSE 0 END)";
            $scoreConditions[] = "(CASE WHEN response LIKE '%$kwSafe%' THEN 1 ELSE 0 END)";
        }
        
        $scoreSql = implode(' + ', $scoreConditions);
        $kwList = implode('|', array_map('preg_quote', $this->keywords));

        // Search in the most conversational ranges first
        $ranges = [
            [1000000, 2000000], // SQuAD / Conversational
            [4000000, 5300000], // Local DB / Patterns
            [1, 1000000]        // Marathi / Others
        ];

        foreach ($ranges as $range) {
            $sql = "SELECT response, ($scoreSql) as score 
                    FROM neural_memory 
                    WHERE id BETWEEN {$range[0]} AND {$range[1]}
                    AND (prompt REGEXP '$kwList' OR response REGEXP '$kwList')
                    ORDER BY score DESC 
                    LIMIT 5";
            
            $res = $this->db->query($sql);
            if (isset($res['data'])) {
                foreach ($res['data'] as $row) {
                    if ($row['score'] < 1) continue;
                    if (!$this->isLikelyMath($row['response'])) return $row['response'];
                }
            }
        }

        // PRIORITY 3: Online Wiki Research (If DB has no high-score match)
        require_once dirname(__DIR__) . '/Tools/Search/WikiSearch.php';
        $wiki = new \Core\Tools\Search\WikiSearch();
        $wikiResult = $wiki->search(implode(' ', $this->keywords));
        if ($wikiResult && !str_contains($wikiResult, "not found")) {
            return substr($wikiResult, 0, 1000); // Return wiki fact
        }
        
        return null;
    }

    private function isLikelyMath(string $text): bool {
        // Detect math patterns like "1 + 1 = 2" or "x = 5"
        // Coding blocks like <?php or python code are NOT math
        if (str_contains($text, '<?php') || str_contains($text, 'def ') || str_contains($text, 'print(')) return false;
        
        return preg_match('/\d+ [\+\-\*\/] \d+ = \d+/u', $text) || 
               (str_contains($text, '=') && preg_match('/\d+/', $text) && strlen($text) < 50);
    }

    private $keywords = [];
    private function extractKeywords($text) {
        static $stopWords = ['is'=>true, 'the'=>true, 'a'=>true, 'an'=>true, 'me'=>true, 'my'=>true, 'what'=>true, 'who'=>true, 'how'=>true, 'where'=>true, 'kyu'=>true, 'kaise'=>true, 'btao'=>true, 'batano'=>true, 'kya'=>true, 'hai'=>true, 'ki'=>true, 'ka'=>true, 'ke'=>true, 'mein'=>true, 'hritik'=>true, 'ai'=>true, 'delhi'=>true, 'toh'=>true, 'aur'=>true, 'thi'=>true, 'tha'=>true];
        $cleanText = strtolower(preg_replace('/[^\p{L}\p{N} ]/u', ' ', $text));
        $words = explode(' ', $cleanText);
        $this->keywords = array_values(array_filter($words, fn($w) => mb_strlen($w) > 2 && !isset($stopWords[$w])));
        return $this->keywords;
    }
}
