<?php
namespace Core\Tools\Terminal;

use Core\Tools\Safety\NeuralSafetyGuard;

class ShellExecutor {
    private NeuralSafetyGuard $guard;

    public function __construct() {
        $this->guard = new NeuralSafetyGuard();
    }

    public function execute(string $command): string {
        $command = trim($command);
        if (!$this->guard->isCommandSafe($command)) {
            return 'Command blocked by safety guard.';
        }

        $output = shell_exec($command . ' 2>&1');
        $outputStr = trim((string)$output);

        // Self-Healing Auto-Recovery
        if (stripos($outputStr, 'Fatal error:') !== false || stripos($outputStr, 'Parse error:') !== false || stripos($outputStr, 'not found') !== false) {
            $debugger = new \Core\Tools\Debugger\NeuralDebugger();
            $debugResponse = $debugger->debug($outputStr);
            $outputStr .= "\n\n[AUTO-RECOVERY SYSTEM TRIGGERED]\n" . $debugResponse;
        }

        return $outputStr;
    }

    public function runPhp(string $path): string {
        $root = dirname(__DIR__, 3);
        $full = preg_match('/^[A-Za-z]:[\\\\\/]/', $path) ? $path : $root . DIRECTORY_SEPARATOR . $path;
        if (!is_file($full)) {
            return 'PHP file not found: ' . $path;
        }

        return $this->execute(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($full));
    }
}
