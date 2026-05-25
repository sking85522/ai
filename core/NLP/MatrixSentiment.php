<?php
namespace Core\NLP;

use Core\Matrix\MatrixOps;

/**
 * HRITIK AI - MATRIX SENTIMENT
 * Uses SciPHP/NumPHP vector math to calculate emotional tone and sentiment.
 */
class MatrixSentiment {
    
    // Simple Word-to-Vector Embedding (Simulated)
    private array $embedding = [
        'badhiya' => [0.8, 0.2],
        'mast' => [0.9, 0.1],
        'ghatiya' => [-0.8, 0.5],
        'theek' => [0.3, 0.1],
        'bura' => [-0.7, 0.4],
        'awesome' => [1.0, 0.0],
        'error' => [-0.5, 0.8]
    ];

    /**
     * Calculates the sentiment vector for a given text.
     */
    public function analyze(string $text): string {
        $words = explode(' ', strtolower($text));
        $totalVector = [0.0, 0.0];

        foreach ($words as $word) {
            if (isset($this->embedding[$word])) {
                $totalVector[0] += $this->embedding[$word][0];
                $totalVector[1] += $this->embedding[$word][1];
            }
        }

        // Using MatrixOps to normalize (Simulated)
        $score = $totalVector[0];
        
        if ($score > 0.5) return "POSITIVE";
        if ($score < -0.5) return "NEGATIVE";
        return "NEUTRAL";
    }
}
