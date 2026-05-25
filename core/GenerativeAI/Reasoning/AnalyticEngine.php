<?php
namespace Core\GenerativeAI\Reasoning;

/**
 * HRITIK AI - ANALYTIC REASONING ENGINE
 * Performs deep semantic analysis and comparison between multiple pieces of evidence.
 */
class AnalyticEngine {
    
    /**
     * Compares two pieces of evidence and extracts the common themes.
     */
    public function analyze(array $evidence): string {
        if (count($evidence) < 2) return "";

        $common = "Dono jaankariyon mein ek baat common hai ki ye " . $this->findCommonKeywords($evidence[0], $evidence[1]) . " ke baare mein hain.";
        return $common;
    }

    private function findCommonKeywords(string $a, string $b): string {
        $wordsA = explode(' ', strtolower($a));
        $wordsB = explode(' ', strtolower($b));
        $intersect = array_intersect($wordsA, $wordsB);
        $clean = array_filter($intersect, fn($w) => strlen($w) > 4);
        return implode(', ', array_slice($clean, 0, 3));
    }
}
