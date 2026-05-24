<?php
namespace Core\Tools\Vision;

class NeuralEyeCore {
    public function analyzeImage(string $path): string {
        $path = trim($path);
        if ($path === '') {
            return '[VISION] Image path missing.';
        }

        $root = dirname(__DIR__, 3);
        $target = preg_match('/^[A-Za-z]:[\\\\\/]/', $path) ? $path : $root . DIRECTORY_SEPARATOR . $path;
        if (!is_file($target)) {
            return '[VISION] Image not found: ' . $path;
        }

        $info = @getimagesize($target);
        if (!$info) {
            return '[VISION] File exists but is not a readable image.';
        }

        return sprintf('[VISION] %s detected. Width=%d, Height=%d, MIME=%s.', basename($target), $info[0], $info[1], $info['mime'] ?? 'unknown');
    }
}
