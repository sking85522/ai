<?php
class LocalKnowledgeFallback
{
    private string $baseDir;
    private CorpusNoiseFilter $noiseFilter;

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? (__DIR__ . '/../../storage/training/local_knowledge');
        $this->noiseFilter = new CorpusNoiseFilter();
    }

    public function findBestQA(string $query, string $language = 'bilingual'): ?array
    {
        if (!is_dir($this->baseDir)) {
            return null;
        }

        $queryNorm = $this->normalize($query);
        if ($queryNorm === '') {
            return null;
        }
        $queryTokens = array_values(array_filter(explode(' ', $queryNorm), static fn($t) => strlen($t) >= 3));

        $files = glob($this->baseDir . DIRECTORY_SEPARATOR . '*.jsonl') ?: [];
        if (!$files) {
            return null;
        }

        $best = null;
        $bestScore = 0.0;

        foreach ($files as $file) {
            $fh = fopen($file, 'rb');
            if (!$fh) {
                continue;
            }

            while (($line = fgets($fh)) !== false) {
                $row = json_decode(trim($line), true);
                if (!is_array($row)) {
                    continue;
                }
                $q = (string) ($row['question'] ?? '');
                $a = (string) ($row['answer'] ?? '');
                if (!$this->noiseFilter->isGoodQA($q, $a)) {
                    continue;
                }

                $rowLang = (string) ($row['language'] ?? 'bilingual');
                if ($language !== 'bilingual' && $rowLang !== 'bilingual' && $rowLang !== $language) {
                    continue;
                }

                $qNorm = $this->normalize($q);
                similar_text($queryNorm, $qNorm, $score);
                $score = (float) $score;
                if (!$this->hasTokenOverlap($queryTokens, $qNorm)) {
                    continue;
                }
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = [
                        'question' => $q,
                        'answer' => $a,
                        'language' => $rowLang,
                        'score' => round($score / 100, 4),
                    ];
                }
            }

            fclose($fh);
        }

        $minScore = count($queryTokens) >= 3 ? 0.64 : 0.56;
        if (!$best || $best['score'] < $minScore) {
            return null;
        }

        return $best;
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        return $text;
    }

    private function hasTokenOverlap(array $queryTokens, string $questionNorm): bool
    {
        if (!$queryTokens) {
            return true;
        }
        foreach (array_slice($queryTokens, 0, 6) as $token) {
            if (str_contains($questionNorm, $token)) {
                return true;
            }
        }
        return false;
    }
}
