<?php
namespace Core\Tools\Optimization;

use Core\Tools\FileSystem\FileEditor;

class SelfOptimizer {
    private FileEditor $files;

    public function __construct() {
        $this->files = new FileEditor();
    }

    public function optimizeFile(string $path): string {
        $read = $this->files->readFile($path, 200000);
        if (($read['status'] ?? 'error') !== 'success') {
            return '[OPTIMIZE] ' . ($read['message'] ?? 'Unable to read file.');
        }

        $content = (string)$read['content'];
        $suggestions = [];
        if (preg_match('/for\s*\([^;]+;\s*[^;]*count\s*\(/', $content)) {
            $suggestions[] = 'Cache count() outside tight for loops.';
        }
        if (substr_count($content, 'require_once') > 10) {
            $suggestions[] = 'Prefer the project autoloader over many manual require_once calls.';
        }
        if (empty($suggestions)) {
            $suggestions[] = 'No obvious quick optimization found.';
        }

        return "[OPTIMIZE] {$read['path']}\n- " . implode("\n- ", $suggestions);
    }

    public function applyAutoPatch(string $path): string {
        $read = $this->files->readFile($path, 500000);
        if (($read['status'] ?? 'error') !== 'success') {
            return '[PATCH] ' . ($read['message'] ?? 'Unable to read file.');
        }

        $content = (string)$read['content'];
        $patched = preg_replace('/[ \t]+$/m', '', $content);
        if ($patched === $content) {
            return "[PATCH] {$read['path']} already clean.";
        }

        $this->files->writeFile((string)$read['path'], (string)$patched);
        return "[PATCH] {$read['path']} trailing whitespace removed.";
    }
}
