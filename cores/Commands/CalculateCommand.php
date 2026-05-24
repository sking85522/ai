<?php
namespace Core\Commands;

use Core\Tools\CalculatorTool;

class CalculateCommand implements CommandInterface {
    private CalculatorTool $calculator;

    public function __construct() {
        $this->calculator = new CalculatorTool();
    }

    public function canProcess(string $task): bool {
        $task = strtolower($task);
        return str_contains($task, 'calculate') || 
               str_contains($task, 'solve') || 
               preg_match('/^[0-9+\-*\/^().\s]+$/', $task) ||
               preg_match('/[0-9]+\s*[\+\-\*\/^]\s*[0-9]+/', $task);
    }

    public function process(string $task): string {
        // Extract the expression (remove command words like calculate/solve)
        $expr = trim(str_replace(['calculate', 'solve', 'calc'], '', strtolower($task)));
        
        $res = $this->calculator->execute(['expression' => $expr]);
        if (($res['status'] ?? '') === 'success') {
            return "[CALCULATOR] Result: " . $res['result'];
        }
        return "[CALCULATOR] Error: " . ($res['message'] ?? 'Could not solve expression.');
    }
}
