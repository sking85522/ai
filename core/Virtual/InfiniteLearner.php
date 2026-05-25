<?php
namespace Core\Virtual;

use Core\Tools\Search\WikiSearch;

/**
 * HRITIK AI - INFINITE LEARNER
 * Background worker that continuously researches new topics and expands the AI's knowledge.
 */
class InfiniteLearner {
    
    private WikiSearch $wiki;
    private LearningCore $learner;

    public function __construct() {
        require_once __DIR__ . '/../Tools/Search/WikiSearch.php';
        require_once __DIR__ . '/LearningCore.php';
        $this->wiki = new WikiSearch();
        $this->learner = new LearningCore();
    }

    /**
     * Performs a random research session.
     */
    public function researchSession(): string {
        $topics = [
            'Quantum Computing', 'Artificial Intelligence 2026', 'SpaceX Starship', 
            'Neural Networks', 'PHP 9 Features', 'Blockchain Technology', 
            'Climate Change Solutions', 'Deep Learning Algorithms'
        ];

        $topic = $topics[array_rand($topics)];
        $data = $this->wiki->search($topic);
        
        if ($data) {
            $this->learner->learn($topic, $data);
            return "[INFINITE_LEARNER] Autonomous knowledge expansion complete for topic: $topic";
        }

        return "[INFINITE_LEARNER] Research session failed for: $topic";
    }
}
