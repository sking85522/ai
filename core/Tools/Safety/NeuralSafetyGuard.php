<?php
namespace Core\Tools\Safety;

class NeuralSafetyGuard {
    private array $blockedCommandPatterns = [
        '/\brm\s+-rf\b/i',
        '/\brmdir\b/i',
        '/\brd\s+\/s\b/i',
        '/\bdel\s+\/[fsq]\b/i',
        '/\bformat\b/i',
        '/\bshutdown\b/i',
        '/\breboot\b/i',
        '/\bmkfs\b/i',
        '/\bdd\s+if=/i',
        '/\bdiskpart\b/i',
        '/\breg\s+(delete|add)\b/i',
        '/\bgit\s+reset\s+--hard\b/i',
        '/\bgit\s+clean\s+-fd\b/i',
        '/\bRemove-Item\b.*\s-Recurse\b/i',
        '/\bSet-ExecutionPolicy\b/i',
        '/\bwget\b/i',
        '/\bcurl\b/i',
        '/\b(?:bash|sh)\s*$/i'
    ];

    public function isCommandSafe(string $command): bool {
        $command = trim($command);
        if ($command === '') {
            return false;
        }

        foreach ($this->blockedCommandPatterns as $pattern) {
            if (preg_match($pattern, $command)) {
                return false;
            }
        }

        return true;
    }

    public function isPathSafe(string $path, string $root): bool {
        $path = $this->normalize($path);
        $root = rtrim($this->normalize($root), DIRECTORY_SEPARATOR);

        return $path === $root || str_starts_with(strtolower($path), strtolower($root . DIRECTORY_SEPARATOR));
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
