<?php
namespace Core\Tools\FileSystem;

use Core\Tools\Safety\NeuralSafetyGuard;

class FileEditor {
    private string $root;
    private NeuralSafetyGuard $guard;

    public function __construct(?string $root = null) {
        $this->root = $this->normalize($root ?: dirname(__DIR__, 3));
        $this->guard = new NeuralSafetyGuard();
    }

    public function readFile(string $path, int $maxBytes = 4000): array {
        try {
            $target = $this->resolve($path);
            if (!is_file($target)) {
                return ['status' => 'error', 'message' => 'File not found.', 'path' => $path];
            }

            $content = file_get_contents($target, false, null, 0, max(1, $maxBytes));
            return [
                'status' => 'success',
                'path' => $this->relative($target),
                'size' => filesize($target),
                'content' => $content === false ? '' : $content
            ];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage(), 'path' => $path];
        }
    }

    public function writeFile(string $path, string $content): bool {
        $target = $this->resolve($path);
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($target, $content) !== false;
    }

    public function appendFile(string $path, string $content): bool {
        $target = $this->resolve($path);
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($target, $content, FILE_APPEND) !== false;
    }

    public function exists(string $path): bool {
        try {
            return file_exists($this->resolve($path));
        } catch (\Throwable) {
            return false;
        }
    }

    public function listFiles(string $path = '.', int $depth = 2): array {
        $base = $this->resolve($path);
        if (!is_dir($base)) {
            return [];
        }

        $items = [];
        $this->walk($base, $items, max(0, $depth), 0);
        return $items;
    }

    public function root(): string {
        return $this->root;
    }

    private function walk(string $dir, array &$items, int $maxDepth, int $level): void {
        if ($level > $maxDepth) {
            return;
        }

        $entries = scandir($dir) ?: [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || $entry === '.git' || $entry === '__pycache__') {
                continue;
            }

            $full = $dir . DIRECTORY_SEPARATOR . $entry;
            $items[] = $this->relative($full) . (is_dir($full) ? DIRECTORY_SEPARATOR : '');
            if (is_dir($full)) {
                $this->walk($full, $items, $maxDepth, $level + 1);
            }
        }
    }

    private function resolve(string $path): string {
        $path = trim($path);
        if ($path === '') {
            throw new \InvalidArgumentException('Path is empty.');
        }

        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $absolute = preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1
            ? $path
            : $this->root . DIRECTORY_SEPARATOR . $path;
        $normalized = $this->normalize($absolute);

        if (!$this->guard->isPathSafe($normalized, $this->root)) {
            throw new \InvalidArgumentException('Path is outside project root.');
        }

        return $normalized;
    }

    private function relative(string $path): string {
        $path = $this->normalize($path);
        if ($path === $this->root) {
            return '.';
        }

        return ltrim(substr($path, strlen($this->root)), DIRECTORY_SEPARATOR);
    }

    private function normalize(string $path): string {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $prefix = '';

        if (preg_match('/^[A-Za-z]:/', $path, $m)) {
            $prefix = strtoupper($m[0]);
            $path = substr($path, 2);
        }

        $parts = [];
        foreach (explode(DIRECTORY_SEPARATOR, $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }

        return $prefix . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
    }
}
