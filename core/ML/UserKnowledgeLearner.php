<?php
class UserKnowledgeLearner
{
    private string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?: (__DIR__ . '/../../storage/training/local_knowledge/user_docs.jsonl');
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        if (!is_file($this->file)) {
            file_put_contents($this->file, '');
        }
    }

    public function ingest(string $input, string $language = 'bilingual'): void
    {
        $text = trim($input);
        if (mb_strlen($text, 'UTF-8') < 30) {
            return;
        }

        $topic = $this->detectTopic($text);
        if ($topic === null) {
            return;
        }

        $answer = $this->firstParagraph($text);
        if ($answer === '') {
            return;
        }

        $rows = [
            ['question' => 'what is ' . $topic, 'answer' => $answer, 'language' => $language],
            ['question' => $topic . ' kya hai', 'answer' => $answer, 'language' => 'bilingual'],
        ];

        $fh = fopen($this->file, 'ab');
        if (!$fh) {
            return;
        }
        foreach ($rows as $row) {
            fwrite($fh, json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL);
        }
        fclose($fh);
    }

    private function detectTopic(string $text): ?string
    {
        if (preg_match('/^\s*([a-z][a-z0-9 \-\+]{2,40})\s+is\b/iu', $text, $m) === 1) {
            return trim(mb_strtolower((string) $m[1], 'UTF-8'));
        }
        if (preg_match('/^\s*([a-z][a-z0-9 \-\+]{2,40})\s*=\s*.+/iu', $text, $m) === 1) {
            return trim(mb_strtolower((string) $m[1], 'UTF-8'));
        }
        if (preg_match('/^\s*([a-z][a-z0-9 \-\+]{2,40})\s*:/iu', $text, $m) === 1) {
            return trim(mb_strtolower((string) $m[1], 'UTF-8'));
        }
        return null;
    }

    private function firstParagraph(string $text): string
    {
        $p = preg_split("/\R\R+/", $text);
        $first = trim((string) ($p[0] ?? $text));
        $first = preg_replace('/\s+/', ' ', $first) ?? $first;
        return mb_substr($first, 0, 500, 'UTF-8');
    }
}
