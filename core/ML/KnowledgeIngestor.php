<?php
class KnowledgeIngestor 
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

    public function ingestFile(string $path, string $source = 'upload'): array
    {
        if (!is_file($path)) {
            return ['ok' => false, 'message' => 'File not found', 'count' => 0];
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($ext) {
            'json' => $this->ingestJson($path, $source),
            'txt' => $this->ingestTxt($path, $source),
            'xml' => $this->ingestXml($path, $source),
            default => ['ok' => false, 'message' => 'Unsupported file type', 'count' => 0],
        };
    }

    private function ingestJson(string $path, string $source): array
    {
        $raw = file_get_contents($path);
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            return ['ok' => false, 'message' => 'Invalid JSON', 'count' => 0];
        }

        $count = 0;
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            $q = trim((string) ($item['question'] ?? $item['prompt'] ?? ''));
            $a = trim((string) ($item['answer'] ?? $item['response'] ?? ''));
            $intent = trim((string) ($item['intent'] ?? 'general'));
            $lang = trim((string) ($item['language'] ?? 'bilingual'));
            if ($q === '' || $a === '') {
                continue;
            }
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }
            $this->knowledgeBase->upsertQA($q, $a, $lang);
            $this->trainingData->add($q, $intent, $lang, 0.8, $source . ':json');
            $count++;
        }
        return ['ok' => true, 'message' => 'JSON ingested', 'count' => $count];
    }

    private function ingestTxt(string $path, string $source): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $count = 0;
        foreach ($lines as $line) {
            $parts = preg_split('/=>|\|/', $line);
            if (!$parts || count($parts) < 2) {
                continue;
            }
            $q = trim((string) $parts[0]);
            $a = trim((string) $parts[1]);
            $intent = isset($parts[2]) ? trim((string) $parts[2]) : 'general';
            $lang = isset($parts[3]) ? trim((string) $parts[3]) : 'bilingual';
            if ($q === '' || $a === '') {
                continue;
            }
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }
            $this->knowledgeBase->upsertQA($q, $a, $lang);
            $this->trainingData->add($q, $intent, $lang, 0.75, $source . ':txt');
            $count++;
        }
        return ['ok' => true, 'message' => 'TXT ingested', 'count' => $count];
    }

    private function ingestXml(string $path, string $source): array
    {
        $xml = @simplexml_load_file($path);
        if (!$xml) {
            return ['ok' => false, 'message' => 'Invalid XML', 'count' => 0];
        }

        $count = 0;
        foreach ($xml->item as $item) {
            $q = trim((string) ($item->question ?? ''));
            $a = trim((string) ($item->answer ?? ''));
            $intent = trim((string) ($item->intent ?? 'general'));
            $lang = trim((string) ($item->language ?? 'bilingual'));
            if ($q === '' || $a === '') {
                continue;
            }
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }
            $this->knowledgeBase->upsertQA($q, $a, $lang);
            $this->trainingData->add($q, $intent, $lang, 0.78, $source . ':xml');
            $count++;
        }
        return ['ok' => true, 'message' => 'XML ingested', 'count' => $count];
    }

    private KnowledgeBase $knowledgeBase;
    private TrainingData $trainingData;
    private CorpusNoiseFilter $noiseFilter;

    public function __construct()
    {
        $this->knowledgeBase = new KnowledgeBase();
        $this->trainingData = new TrainingData();
        $this->noiseFilter = new CorpusNoiseFilter();
    }

    public function ingestFile(string $path, string $source = 'upload'): array
    {
        if (!is_file($path)) {
            return ['ok' => false, 'message' => 'File not found', 'count' => 0];
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($ext) {
            'json' => $this->ingestJson($path, $source),
            'txt' => $this->ingestTxt($path, $source),
            'xml' => $this->ingestXml($path, $source),
            default => ['ok' => false, 'message' => 'Unsupported file type', 'count' => 0],
        };
    }

    private function ingestJson(string $path, string $source): array
    {
        $raw = file_get_contents($path);
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            return ['ok' => false, 'message' => 'Invalid JSON', 'count' => 0];
        }

        $count = 0;
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            $q = trim((string) ($item['question'] ?? $item['prompt'] ?? ''));
            $a = trim((string) ($item['answer'] ?? $item['response'] ?? ''));
            $intent = trim((string) ($item['intent'] ?? 'general'));
            $lang = trim((string) ($item['language'] ?? 'bilingual'));
            if ($q === '' || $a === '') {
                continue;
            }
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }
            $this->knowledgeBase->upsertQA($q, $a, $lang);
            $this->trainingData->add($q, $intent, $lang, 0.8, $source . ':json');
            $count++;
        }
        return ['ok' => true, 'message' => 'JSON ingested', 'count' => $count];
    }

    private function ingestTxt(string $path, string $source): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $count = 0;
        foreach ($lines as $line) {
            $parts = preg_split('/=>|\|/', $line);
            if (!$parts || count($parts) < 2) {
                continue;
            }
            $q = trim((string) $parts[0]);
            $a = trim((string) $parts[1]);
            $intent = isset($parts[2]) ? trim((string) $parts[2]) : 'general';
            $lang = isset($parts[3]) ? trim((string) $parts[3]) : 'bilingual';
            if ($q === '' || $a === '') {
                continue;
            }
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }
            $this->knowledgeBase->upsertQA($q, $a, $lang);
            $this->trainingData->add($q, $intent, $lang, 0.75, $source . ':txt');
            $count++;
        }
        return ['ok' => true, 'message' => 'TXT ingested', 'count' => $count];
    }

    private function ingestXml(string $path, string $source): array
    {
        $xml = @simplexml_load_file($path);
        if (!$xml) {
            return ['ok' => false, 'message' => 'Invalid XML', 'count' => 0];
        }

        $count = 0;
        foreach ($xml->item as $item) {
            $q = trim((string) ($item->question ?? ''));
            $a = trim((string) ($item->answer ?? ''));
            $intent = trim((string) ($item->intent ?? 'general'));
            $lang = trim((string) ($item->language ?? 'bilingual'));
            if ($q === '' || $a === '') {
                continue;
            }
            if (!$this->noiseFilter->isGoodQA($q, $a)) {
                continue;
            }
            $this->knowledgeBase->upsertQA($q, $a, $lang);
            $this->trainingData->add($q, $intent, $lang, 0.78, $source . ':xml');
            $count++;
        }
        return ['ok' => true, 'message' => 'XML ingested', 'count' => $count];
    }
}