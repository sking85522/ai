<?php
namespace Core\Training\Auto;

use Core\Engine\MainEngine;

/**
 * HRITIK AI - AUTONOMOUS PROBER
 * Sends prompts to the AI engine in a loop until it successfully generates a valid program.
 */
class AutonomousProber {
    
    private MainEngine $engine;
    private array $testPrompts = [
        "Write a PHP function to sort an array",
        "Create a Python script that calculates factorials",
        "Generate a simple HTML/CSS login page",
        "Write a Java class for a library system",
        "Make a PHP script to connect to MySQL"
    ];

    public function __construct() {
        $this->engine = new MainEngine();
    }

    /**
     * Start the probing cycle.
     */
    public function probe(): string {
        $output = "[PROBER] Initiating Autonomous Validation Cycle...\n";
        
        foreach ($this->testPrompts as $prompt) {
            $output .= "> Probing: '$prompt'\n";
            $result = $this->engine->processPrompt($prompt);
            $response = $result['response'] ?? '';

            if ($this->isValidProgram($response)) {
                return $output . "[SUCCESS] Hritik AI successfully generated a program!\nContent:\n" . substr($response, 0, 200) . "...";
            }
            
            $output .= "  x Failed: Response was not a valid program. Retrying...\n";
        }

        return $output . "[FAILURE] Could not trigger autonomous program generation in this cycle.";
    }

    private function isValidProgram(string $text): bool {
        // Look for common code markers
        $markers = ['<?php', 'def ', 'class ', 'import ', 'function ', '<html>', 'var ', 'public static void main'];
        foreach ($markers as $marker) {
            if (str_contains($text, $marker)) return true;
        }
        return false;
    }
}
