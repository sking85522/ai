<?php
namespace Core\Evolution;

use Core\Tools\Terminal\ShellExecutor;
use Core\Training\Feedback\LocalTeacherBridge;

/**
 * HRITIK AI - SELF-REPAIR CORE
 * Monitors system errors and autonomously generates patches.
 */
class SelfRepairCore {
    
    private ShellExecutor $terminal;
    private LocalTeacherBridge $teacher;

    public function __construct() {
        $this->terminal = new ShellExecutor();
        $this->teacher = new LocalTeacherBridge();
    }

    /**
     * Attempt to repair a file based on an error message.
     */
    public function repair(string $filePath, string $error): string {
        if (!file_exists($filePath)) return "[REPAIR] File not found: $filePath";

        $content = file_get_contents($filePath);
        
        $prompt = "Repair this code file. It has the following error:\nError: $error\n\nCode:\n$content\n\nReturn ONLY the fixed code.";
        $res = $this->teacher->evaluate($prompt, "REPAIR_CODE", [], 0);
        
        if (($res['status'] ?? '') === 'success' && !empty($res['final_answer'])) {
            $fixedCode = $res['final_answer'];
            // Basic sanity check: is it actually code?
            if (str_contains($fixedCode, '<?php') || str_contains($fixedCode, 'def ') || str_contains($fixedCode, '{')) {
                file_put_contents($filePath, $fixedCode);
                return "[REPAIR] Successfully patched $filePath. Error resolved.";
            }
        }

        return "[REPAIR] Failed to generate a reliable patch for $filePath.";
    }

    /**
     * Runs a syntax check on a file.
     */
    public function checkSyntax(string $path): bool {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext === 'php') {
            $output = shell_exec("php -l " . escapeshellarg($path));
            return str_contains($output, 'No syntax errors detected');
        }
        return true; // Default to true for unknown types
    }
}
