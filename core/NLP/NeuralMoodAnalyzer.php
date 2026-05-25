<?php
namespace Core\NLP;

use Core\SQLGenerator\SQLGenerator;

/**
 * HRITIK AI - NEURAL MOOD ANALYZER
 * Detects the emotional state of the user and fetches dynamic tone prefixes from the DB.
 */
class NeuralMoodAnalyzer {
    
    private array $patterns = [
        'happy' => ['waah', 'maza', 'vadiya', 'sahi', 'good', 'happy', 'dhanyawad', 'shukriya', 'great', 'awesome', 'mast', 'jhakaas', 'smile', '😊', '😂', '🔥'],
        'angry' => ['bakwas', 'pagal', 'gadhe', 'irritate', 'gussa', 'bekar', 'fail', 'bad', 'nonsense', 'kachra', 'hat', 'shut up', 'fokat', '😠', '😡'],
        'sad' => ['dukh', 'sad', 'rona', 'tension', 'problem', 'pareshan', 'akela', 'bore', 'man nahi', 'thak gaya', '😭', '😞', '💔'],
        'curious' => ['kyun', 'kaise', 'kab', 'kahan', 'who', 'what', 'how', 'why', 'btao', 'shikhao', 'info', 'matlab', 'kya', '🤔', '🧐']
    ];

    /**
     * Analyzes the text and returns the dominant mood.
     */
    public function analyze(string $text): string {
        $text = strtolower($text);
        $scores = ['happy' => 0, 'angry' => 0, 'sad' => 0, 'curious' => 0];

        foreach ($this->patterns as $mood => $keywords) {
            foreach ($keywords as $word) {
                $pattern = '/(^|[^a-z0-9])' . preg_quote($word, '/') . '($|[^a-z0-9])/i';
                if (preg_match($pattern, $text)) {
                    $scores[$mood]++;
                }
            }
        }

        arsort($scores);
        $topMood = key($scores);
        return $scores[$topMood] > 0 ? $topMood : 'neutral';
    }

    /**
     * Fetches a tone prefix from the Database based on the mood.
     */
    public function getTonePrefix(string $mood): string {
        if ($mood === 'neutral') return "";

        global $db;
        if (!isset($db) || $db === null) return "";

        $sqlGen = new SQLGenerator();
        $sql = $sqlGen->generate('search_knowledge', ['query' => "tone_$mood"]);
        $res = $db->query($sql);
        
        return $res['data'][0]['k_value'] ?? "";
    }
}
