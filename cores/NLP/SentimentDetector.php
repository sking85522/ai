<?php
namespace Core\NLP;

if (file_exists(dirname(__DIR__, 2) . '/modules/nlphp/autoload.php')) {
    require_once dirname(__DIR__, 2) . '/modules/nlphp/autoload.php';
}

use NLPHP\NLPHP;

class SentimentDetector {
    /**
     * Analyzes the sentiment of a given text.
     * Returns an array: ['label' => 'positive|negative|neutral', 'score' => ...]
     */
    public function analyze(string $text): array {
        if (!class_exists('NLPHP\NLPHP')) {
            return ['label' => 'neutral', 'score' => 0.5];
        }

        return NLPHP::sentiment($text);
    }

    /**
     * Get a simple label.
     */
    public function getLabel(string $text): string {
        $result = $this->analyze($text);
        return $result['label'] ?? 'neutral';
    }

    public function detect(string $text): string {
        return $this->getLabel($text);
    }
}
