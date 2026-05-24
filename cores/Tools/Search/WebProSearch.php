<?php
namespace Core\Tools\Search;

class WebProSearch {
    public function researchCode(string $topic): string {
        $topic = trim($topic);
        if ($topic === '') {
            return '[RESEARCH] Topic missing.';
        }

        $summary = null;
        if (class_exists('\Core\API\ExternalIntelligence')) {
            try {
                $summary = (new \Core\API\ExternalIntelligence())->search($topic);
            } catch (\Throwable) {
                $summary = null;
            }
        }

        if (!$summary) {
            $summary = 'No live source returned a concise answer. Break the topic into definition, API surface, examples, risks, and tests.';
        }

        return "[RESEARCH] Topic: {$topic}\n" .
               "Summary: {$summary}\n" .
               "Next steps: verify official docs, build a small proof of concept, then add tests around edge cases.";
    }
}
