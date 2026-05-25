<?php
namespace Core\Training\LanguageModel;

use Core\Engine\MainEngine;
use Core\Response\ResponseQualityGuard;

require_once dirname(__DIR__, 2) . '/Bootstrap.php';

class PromptBenchmark {
    private ResponseQualityGuard $guard;

    public function __construct() {
        $this->guard = new ResponseQualityGuard();
    }

    public function run(MainEngine $engine, array $cases, int $limit = 1000): array {
        $cases = array_slice($cases, 0, $limit);
        $results = [];
        $pass = 0;

        foreach ($cases as $i => $case) {
            $prompt = trim((string)($case['prompt'] ?? $case['question'] ?? ''));
            $expected = trim((string)($case['response'] ?? $case['answer'] ?? ''));
            if ($prompt === '' || $expected === '') {
                continue;
            }

            $reply = $engine->processPrompt($prompt, 'benchmark_session_' . $i);
            $actual = (string)($reply['response'] ?? '');
            $ok = $this->matches($expected, $actual);
            if ($ok) {
                $pass++;
            }

            $results[] = [
                'prompt' => $prompt,
                'expected' => $expected,
                'actual' => $actual,
                'pass' => $ok,
            ];
        }

        $count = count($results);
        return [
            'total' => $count,
            'passed' => $pass,
            'accuracy' => $count > 0 ? round(($pass / $count) * 100, 2) : 0.0,
            'results' => $results,
        ];
    }

    private function matches(string $expected, string $actual): bool {
        $expected = strtolower($this->guard->clean($expected));
        $actual = strtolower($this->guard->clean($actual));
        if ($expected === '' || $actual === '') {
            return false;
        }

        if (str_contains($actual, $expected) || str_contains($expected, $actual)) {
            return true;
        }

        similar_text($expected, $actual, $percent);
        return $percent >= 55.0;
    }
}
