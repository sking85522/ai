<?php
class TaskPlanner
{
    public function makePlan(string $input): array
    {
        $x = mb_strtolower(trim($input), 'UTF-8');
        $steps = [];

        $steps[] = ['tool' => 'analyze', 'label' => 'Analyze request'];

        if (preg_match('/https?:\/\/[^\s]+/i', $input) === 1) {
            $steps[] = ['tool' => 'ingest_url', 'label' => 'Open URL and ingest content'];
        }
        if (str_contains($x, 'capital')) {
            $steps[] = ['tool' => 'capital_query', 'label' => 'Resolve capital query'];
        }
        if (preg_match('/\b(math|sin|cos|tan|integral|derivative|vector)\b/u', $x) === 1) {
            $steps[] = ['tool' => 'math', 'label' => 'Run math evaluator'];
        }
        if (preg_match('/\b(quiz|mcq|test)\b/u', $x) === 1) {
            $steps[] = ['tool' => 'quiz', 'label' => 'Generate quiz'];
        }
        if (preg_match('/\b(code|write|debug|fix|function|class|snippet|script)\b/u', $x) === 1) {
            $steps[] = ['tool' => 'code', 'label' => 'Generate code/debug output'];
        }

        $steps[] = ['tool' => 'web_search', 'label' => 'Search web and gather facts'];
        $steps[] = ['tool' => 'verify', 'label' => 'Verify and compose final answer'];

        return $steps;
    }
}
