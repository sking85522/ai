<?php
namespace Core\Tools\Git;

use Core\Tools\Terminal\ShellExecutor;

class GitPro {
    private ShellExecutor $shell;

    public function __construct() {
        $this->shell = new ShellExecutor();
    }

    public function init(string $path): string {
        $path = trim($path) ?: '.';
        $root = dirname(__DIR__, 3);
        $target = preg_match('/^[A-Za-z]:[\\\\\/]/', $path) ? $path : $root . DIRECTORY_SEPARATOR . $path;
        if (!is_dir($target)) {
            return '[GIT] Folder not found: ' . $path;
        }

        $cmd = 'git -C ' . escapeshellarg($target) . ' init';
        return "[GIT]\n" . $this->shell->execute($cmd);
    }

    public function commitChanges(string $message): string {
        $message = trim($message) ?: 'Hritik AI update';
        $root = dirname(__DIR__, 3);
        $status = $this->shell->execute('git -C ' . escapeshellarg($root) . ' status --short');
        if (str_contains($status, 'not a git repository')) {
            return '[GIT] Current project is not a git repository.';
        }
        if (trim($status) === '') {
            return '[GIT] No changes to commit.';
        }

        $this->shell->execute('git -C ' . escapeshellarg($root) . ' add .');
        $out = $this->shell->execute('git -C ' . escapeshellarg($root) . ' commit -m ' . escapeshellarg($message));
        return "[GIT]\n" . $out;
    }

    public function pullChanges(string $branch = 'main'): string {
        $root = dirname(__DIR__, 3);
        $cmd = 'git -C ' . escapeshellarg($root) . ' pull origin ' . escapeshellarg($branch);
        $out = $this->shell->execute($cmd);
        return "[GIT PULL]\n" . $out;
    }
}
