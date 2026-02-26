<?php
class DownloadedDatasetImporter
{
    private KnowledgeBase $knowledgeBase;
    private TrainingData $trainingData;
    private CorpusNoiseFilter $noiseFilter;

    public function __construct()
    {
        $this->knowledgeBase = new KnowledgeBase();
        $this->trainingData = new TrainingData();
        $this->noiseFilter = new CorpusNoiseFilter();
    }

    public function importFolder(string $folder, array $limits = []): array
    {
        $limits = array_merge([
            'coding' => 2000,
            'squad' => 12000,
            'news' => 3000,
        ], $limits);

        $stats = [
            'coding' => 0,
            'squad' => 0,
            'news' => 0,
            'errors' => [],
        ];

        $codingPath = $folder . DIRECTORY_SEPARATOR . 'ai_coding_dataset_large.json';
        if (is_file($codingPath)) {
            try {
                $stats['coding'] = $this->importCodingJson($codingPath, (int) $limits['coding']);
            } catch (Throwable $e) {
                $stats['errors'][] = 'coding: ' . $e->getMessage();
            }
        }

        $squadPath = $folder . DIRECTORY_SEPARATOR . 'train-v2.0.json';
        if (is_file($squadPath)) {
            try {
                $stats['squad'] = $this->importSquad($squadPath, (int) $limits['squad']);
            } catch (Throwable $e) {
                $stats['errors'][] = 'squad: ' . $e->getMessage();
            }
        }

        $newsPath = $folder . DIRECTORY_SEPARATOR . '20news-18828';
        if (is_dir($newsPath)) {
            try {
                $stats['news'] = $this->import20News($newsPath, (int) $limits['news']);
            } catch (Throwable $e) {
                $stats['errors'][] = '20news: ' . $e->getMessage();
            }
        }

        return $stats;
    }

    private function importCodingJson(string $path, int $limit): int
    {
        $raw = file_get_contents($path);
        $rows = json_decode((string) $raw, true);
        if (!is_array($rows)) {
            return 0;
        }

        $count = 0;
        foreach ($rows as $row) {
            if ($count >= $limit) {
                break;
            }
            if (!is_array($row)) {
                continue;
            }
            $q = trim((string) ($row['prompt'] ?? ''));
            $a = trim((string) ($row['ai_response'] ?? ''));
            $language = $this->normalizeLanguage((string) ($row['language'] ?? 'en'));
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }

            $this->knowledgeBase->upsertQA($q, $a, $language);
            $this->trainingData->add($q, 'coding_help', $language, 0.78, 'downloadaitrainfile:coding');
            $count++;
        }
        return $count;
    }

    private function importSquad(string $path, int $limit): int
    {
        $raw = file_get_contents($path);
        $doc = json_decode((string) $raw, true);
        if (!is_array($doc) || !isset($doc['data']) || !is_array($doc['data'])) {
            return 0;
        }

        $count = 0;
        foreach ($doc['data'] as $article) {
            if ($count >= $limit) {
                break;
            }
            $paragraphs = $article['paragraphs'] ?? [];
            if (!is_array($paragraphs)) {
                continue;
            }
            foreach ($paragraphs as $paragraph) {
                if ($count >= $limit) {
                    break;
                }
                $qas = $paragraph['qas'] ?? [];
                if (!is_array($qas)) {
                    continue;
                }
                foreach ($qas as $qa) {
                    if ($count >= $limit) {
                        break;
                    }
                    if (($qa['is_impossible'] ?? false) === true) {
                        continue;
                    }
                    $q = trim((string) ($qa['question'] ?? ''));
                    $answers = $qa['answers'] ?? [];
                    if (!$q || !is_array($answers) || !$answers) {
                        continue;
                    }
                    $a = trim((string) ($answers[0]['text'] ?? ''));
                    if (!$this->noiseFilter->isGoodQA($q, $a)) {
                        continue;
                    }

                    $this->knowledgeBase->upsertQA($q, $a, 'en');
                    $this->trainingData->add($q, 'knowledge_answer', 'en', 0.9, 'downloadaitrainfile:squad');
                    $count++;
                }
            }
        }
        return $count;
    }

    private function import20News(string $folder, int $limit): int
    {
        $groupDirs = glob($folder . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];
        $count = 0;

        foreach ($groupDirs as $groupDir) {
            if ($count >= $limit) {
                break;
            }
            $group = basename($groupDir);
            $files = glob($groupDir . DIRECTORY_SEPARATOR . '*') ?: [];
            foreach ($files as $file) {
                if ($count >= $limit) {
                    break;
                }
                if (!is_file($file)) {
                    continue;
                }
                $raw = file_get_contents($file);
                if ($raw === false) {
                    continue;
                }

                $subject = $this->extractHeader($raw, 'Subject');
                $body = $this->extractBody($raw);
                if ($body === '') {
                    continue;
                }
                $body = mb_substr($body, 0, 500, 'UTF-8');

                $intent = 'topic_' . preg_replace('/[^a-z0-9_]+/i', '_', strtolower($group));
                $question = $subject !== '' ? $subject : ('tell me about ' . $group);
                $answer = $body;
                if (!$this->noiseFilter->isGoodQA($question, $answer)) {
                    continue;
                }

                $this->knowledgeBase->upsertQA($question, $answer, 'en');
                $this->trainingData->add($question, $intent, 'en', 0.66, 'downloadaitrainfile:20news');
                $count++;
            }
        }
        return $count;
    }

    private function extractHeader(string $raw, string $key): string
    {
        if (preg_match('/^' . preg_quote($key, '/') . ':\s*(.+)$/mi', $raw, $m) === 1) {
            return trim((string) $m[1]);
        }
        return '';
    }

    private function extractBody(string $raw): string
    {
        $parts = preg_split("/\R\R/", $raw, 2);
        $body = $parts[1] ?? $raw;
        $body = preg_replace('/\s+/', ' ', trim($body)) ?? '';
        return $body;
    }

    private function normalizeLanguage(string $language): string
    {
        $value = strtolower(trim($language));
        if ($value === 'hindi' || $value === 'hi') {
            return 'hi';
        }
        if ($value === 'english' || $value === 'en') {
            return 'en';
        }
        return 'en';
    }
}
