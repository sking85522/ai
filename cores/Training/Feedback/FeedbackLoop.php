<?php
namespace Core\Training\Feedback;

use Core\Memory\RAG\LocalRAG;

class FeedbackLoop {
    private LocalRAG $rag;

    public function __construct(?LocalRAG $rag = null, ?string $logPath = null) {
        $this->rag = $rag ?: new LocalRAG();
    }

    public function record(string $prompt, string $originalResponse, string $finalResponse, array $meta = []): void {
        if (getenv('HRITIK_FEEDBACK_ENABLED') === '0') {
            return;
        }

        $prompt = trim((string)preg_replace('/^\xEF\xBB\xBF/u', '', $prompt));
        $record = [
            'timestamp' => date('c'),
            'prompt' => $prompt,
            'original_response' => $originalResponse,
            'final_response' => $finalResponse,
            'meta' => $meta
        ];
        $this->saveRemoteFeedbackRecord($record);

        $source = (string)($meta['source'] ?? 'feedback_loop');
        $confidence = (float)($meta['confidence'] ?? 0.0);
        if ($confidence >= 0.62 && !$this->rag->isWeak($finalResponse)) {
            $this->rag->upsert($prompt, $finalResponse, $source, min(0.95, max(0.7, $confidence)));
            $this->saveRemoteVerifiedQA($prompt, $finalResponse, $source);
        }
    }

    private function saveRemoteVerifiedQA(string $prompt, string $answer, string $source): void {
        if (getenv('HRITIK_DISABLE_REMOTE_DB') === '1') {
            return;
        }

        global $db;
        if (!isset($db) || $db === null) {
            return;
        }

        $safePrompt = addslashes($prompt);
        $safeAnswer = addslashes($answer);
        $safeSource = addslashes($source);
        $sql = "INSERT INTO neural_knowledge (category, sub_category, k_key, k_value) " .
               "VALUES ('verified_qa', '{$safeSource}', '{$safePrompt}', '{$safeAnswer}')";
        try {
            $db->query($sql);
        } catch (\Throwable) {
            // Local learning is the primary source; remote write is best-effort.
        }
    }

    private function saveRemoteFeedbackRecord(array $record): void {
        global $db;
        if (!isset($db) || $db === null) {
            return;
        }

        $safePrompt = addslashes((string)($record['prompt'] ?? ''));
        $safeOriginal = addslashes((string)($record['original_response'] ?? ''));
        $safeFinal = addslashes((string)($record['final_response'] ?? ''));
        $metaJson = addslashes(json_encode($record['meta'] ?? [], JSON_UNESCAPED_SLASHES));
        $timestamp = addslashes((string)($record['timestamp'] ?? date('c')));

        $sql = "INSERT INTO neural_feedback_log (event_time, prompt, original_response, final_response, meta_json) " .
               "VALUES ('{$timestamp}', '{$safePrompt}', '{$safeOriginal}', '{$safeFinal}', '{$metaJson}')";
        $res = $db->query($sql);
        if (($res['status'] ?? '') === 'error') {
            $sql = "INSERT INTO neural_knowledge (category, sub_category, k_key, k_value) VALUES " .
                   "('feedback_log', 'json_record', '{$safePrompt}', '" . addslashes(json_encode($record, JSON_UNESCAPED_SLASHES)) . "')";
            $db->query($sql);
        }
    }
}
