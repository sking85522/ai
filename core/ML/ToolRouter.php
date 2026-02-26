<?php
class ToolRouter
{
    public function __construct(
        private ?WebKnowledgeClient $webKnowledgeClient,
        private MathEvaluator $mathEvaluator,
        private CodeComposer $codeComposer,
        private QuizGenerator $quizGenerator,
        private TableGenerator $tableGenerator,
        private UrlContentIngestor $urlContentIngestor
    ) {
    }

    public function run(string $tool, string $input, string $language = 'bilingual'): ?string
    {
        return match ($tool) {
            'analyze' => 'Request analyzed successfully.',
            'ingest_url' => $this->runIngestUrl($input, $language),
            'capital_query' => $this->webKnowledgeClient?->answerCapitalQuery($input),
            'math' => $this->runMath($input),
            'quiz' => $this->quizGenerator->generate($input, $language),
            'code' => $this->runCode($input, $language),
            'web_search' => $this->webKnowledgeClient?->answerCapitalQuery($input) ?? $this->webKnowledgeClient?->answerWebSnippet($input),
            default => null,
        };
    }

    private function runMath(string $input): ?string
    {
        $res = $this->mathEvaluator->evaluateMathQuery($input);
        if ($res === null) {
            return null;
        }
        return 'Math result: ' . (is_float($res) ? (string) round($res, 6) : (string) $res);
    }

    private function runCode(string $input, string $language): string
    {
        $res = $this->codeComposer->compose($input, $language);
        return (string) (($res['title'] ?? 'Code Output') . "\n\n" . ($res['body'] ?? ''));
    }

    private function runIngestUrl(string $input, string $language): ?string
    {
        if (preg_match('/https?:\/\/[^\s]+/i', $input, $m) !== 1) {
            return null;
        }
        $url = trim((string) $m[0]);
        $ing = $this->urlContentIngestor->ingest($url);
        if (!$ing) {
            return null;
        }
        $title = trim((string) ($ing['title'] ?? 'Unknown'));
        $summary = trim((string) ($ing['summary'] ?? ''));
        return ($language === 'hi'
            ? ('लिंक पढ़ लिया: ' . $title)
            : ('URL parsed: ' . $title)
        ) . "\n" . mb_substr($summary, 0, 260, 'UTF-8');
    }
}
