<?php
namespace Core\Training;

class DataQualityCleaner {
    private array $weakPhrases = [
        'study karni',
        'exact jawab nahi',
        'data bank',
        'smarter ho raha',
        'time dijiye',
        'training le raha'
    ];

    public function dryRun(int $limit = 50): array {
        $remote = $this->scanRemote($limit);

        return [
            'mode' => 'dry_run',
            'remote_candidates' => $remote,
            'recommendation' => 'All knowledge/training data is DB-only. Quarantine weak rows after backup; keep verified_qa as priority.'
        ];
    }

    public function quarantinePlan(): string {
        $report = $this->dryRun(20);
        return "[DATA CLEANER]\n" .
               "Remote weak candidates sampled: " . count($report['remote_candidates']) . "\n" .
               "Action: verified answers are saved in DB category=verified_qa. Weak rows should be quarantined in DB after backup.";
    }

    private function scanRemote(int $limit): array {
        if (getenv('HRITIK_DISABLE_REMOTE_DB') === '1') {
            return [];
        }

        global $db;
        if (!isset($db) || $db === null) {
            return [];
        }

        $conditions = [];
        foreach ($this->weakPhrases as $phrase) {
            $conditions[] = "LOWER(k_value) LIKE '%" . addslashes($phrase) . "%'";
        }

        $sql = "SELECT k_key, k_value, category FROM neural_knowledge WHERE " . implode(' OR ', $conditions) . " LIMIT " . max(1, $limit);
        try {
            $res = $db->query($sql);
            return $res['data'] ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function isWeak(string $answer): bool {
        foreach ($this->weakPhrases as $phrase) {
            if (str_contains($answer, $phrase)) {
                return true;
            }
        }

        return false;
    }
}
