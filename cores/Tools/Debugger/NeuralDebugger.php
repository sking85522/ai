<?php
namespace Core\Tools\Debugger;

use Core\Tools\FileSystem\FileEditor;

class NeuralDebugger {
    private FileEditor $files;

    public function __construct() {
        $this->files = new FileEditor();
    }

    public function debug(string $error): string {
        $error = trim($error);
        if ($error === '') {
            return '[DEBUG] Please provide an error message.';
        }

        $tips = [];
        if (stripos($error, 'class') !== false && stripos($error, 'not found') !== false) {
            $tips[] = 'Check namespace, file path, and autoloader prefix.';
        }
        if (stripos($error, 'undefined variable') !== false) {
            $tips[] = 'Initialize the variable before use and check branch coverage.';
        }
        if (stripos($error, 'call to undefined method') !== false) {
            $tips[] = 'Confirm the object type and method name at the call site.';
        }
        if (empty($tips)) {
            $tips[] = 'Reproduce with display_errors=1, isolate the smallest failing input, then inspect the stack trace top-down.';
        }

        return "[DEBUG]\nError: {$error}\nLikely fix: " . implode(' ', $tips);
    }

    public function auditFile(string $path): string {
        $read = $this->files->readFile($path, 200000);
        if (($read['status'] ?? 'error') !== 'success') {
            return '[AUDIT] ' . ($read['message'] ?? 'Unable to read file.');
        }

        $issues = [];
        $content = (string)$read['content'];
        if (str_ends_with(strtolower($path), '.php')) {
            $tmp = tempnam(sys_get_temp_dir(), 'hritik_lint_');
            file_put_contents($tmp, $content);
            $lint = shell_exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($tmp) . ' 2>&1');
            @unlink($tmp);
            if ($lint && stripos($lint, 'No syntax errors detected') === false) {
                $issues[] = trim($lint);
            }
        }

        if (preg_match('/\beval\s*\(/i', $content)) {
            $issues[] = 'Uses eval(); replace with explicit parsing or whitelisted dispatch.';
        }
        if (preg_match('/\$_(GET|POST|REQUEST)\b/', $content) && !preg_match('/htmlspecialchars|filter_input|filter_var/', $content)) {
            $issues[] = 'Reads request data without visible validation/sanitization.';
        }

        if (empty($issues)) {
            return "[AUDIT] {$read['path']} looks usable. No high-risk issue found in the quick audit.";
        }

        return "[AUDIT] {$read['path']} issues:\n- " . implode("\n- ", $issues);
    }
}
