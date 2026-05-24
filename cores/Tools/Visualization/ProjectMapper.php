<?php
namespace Core\Tools\Visualization;

class ProjectMapper {
    private string $root;

    public function __construct(?string $root = null) {
        $this->root = $root ?: dirname(__DIR__, 3);
    }

    public function generateTree(int $maxDepth = 3): string {
        $lines = [basename($this->root) . DIRECTORY_SEPARATOR];
        $this->appendTree($this->root, $lines, 0, max(1, $maxDepth));
        return implode("\n", $lines);
    }

    public function auditConnections(): string {
        $requiredFolders = [
            'API', 'DL', 'DataHandling', 'Engine', 'Evaluation', 'Evolution',
            'GenerativeAI', 'Lang', 'Learning', 'ML', 'MachineLearningAlgorithms',
            'Matrix', 'NLU', 'NeuralSchema', 'Parallel', 'Response',
            'SpeechProcessing', 'Training', 'Virtual', 'pro_lang'
        ];

        $checks = [];
        foreach ($requiredFolders as $folder) {
            $path = $this->root . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . $folder;
            $count = is_dir($path) ? count(glob($path . DIRECTORY_SEPARATOR . '*.php') ?: []) : 0;
            $checks[] = sprintf('[%s] core/%s (%d direct PHP files)', is_dir($path) ? 'OK' : 'MISS', $folder, $count);
        }

        $toolRegistry = is_file($this->root . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'ToolRegistry.php')
            ? '[OK] core/Tools registry'
            : '[MISS] core/Tools registry';
        array_unshift($checks, $toolRegistry);

        return "[SYSTEM AUDIT]\n" . implode("\n", $checks);
    }

    private function appendTree(string $dir, array &$lines, int $level, int $maxDepth): void {
        if ($level >= $maxDepth) {
            return;
        }

        $entries = array_values(array_filter(scandir($dir) ?: [], function ($entry) {
            return !in_array($entry, ['.', '..', '.git', '__pycache__'], true);
        }));

        sort($entries, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($entries as $entry) {
            $full = $dir . DIRECTORY_SEPARATOR . $entry;
            $lines[] = str_repeat('  ', $level + 1) . '- ' . $entry . (is_dir($full) ? DIRECTORY_SEPARATOR : '');
            if (is_dir($full)) {
                $this->appendTree($full, $lines, $level + 1, $maxDepth);
            }
        }
    }
}
