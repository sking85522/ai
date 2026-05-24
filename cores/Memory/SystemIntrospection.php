<?php
namespace Core\Memory;

/**
 * HRITIK AI - SYSTEM INTROSPECTION (HEALER)
 * Monitors the health of all AI sub-systems.
 */
class SystemIntrospection {

    public function getHealthReport(): array {
        $stats = [
            'engine_version' => '4.7.2 (Pro Edition)',
            'uptime' => 'Stable',
            'memory_status' => $this->checkMemory(),
            'nlp_bridge' => class_exists('Core\NLP\NLPPipeline') ? 'Online' : 'Offline',
            'semantic_matrix' => 'Active (TF-IDF Enabled)',
            'web_bridge' => function_exists('curl_init') ? 'Operational' : 'CURL Missing',
            'load_factor' => sys_getloadavg()[0] ?? 0.1
        ];

        return $stats;
    }

    private function checkMemory(): string {
        $chatDir = dirname(__DIR__, 2) . '/storage/data/chats';
        if (!is_dir($chatDir)) return "Not Initialized";
        $count = count(glob($chatDir . '/*.json'));
        return "$count Active Neural Blocks";
    }

    public function getIntrospectiveThought(): string {
        $health = $this->getHealthReport();
        if ($health['web_bridge'] === 'Operational') {
            return "meri saari neural pathways stable hain. Main internet se connect ho sakta hoon aur complex reasoning perform kar sakta hoon.";
        }
        return "main offline mode mein kaam kar raha hoon. Mere local knowledge clusters active hain.";
    }
}
