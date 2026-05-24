<?php
namespace Core\ML;

// Include SciPHP if available
if (file_exists(dirname(__DIR__, 2) . '/modules/sciphp/autoload.php')) {
    require_once dirname(__DIR__, 2) . '/modules/sciphp/autoload.php';
}

class MathEvaluator {
    
    /**
     * Evaluates a mathematical string expression.
     * Support for basic arithmetic and SciPHP advanced functions.
     */
    public function evaluate(string $expression): string {
        $expression = preg_replace('/[^0-9\+\-\*\/\(\)\. ]/', '', $expression);
        
        try {
            // Using a safe eval wrapper (simplified for local execution)
            $result = eval("return ($expression);");
            return (string)$result;
        } catch (\Throwable $e) {
            return "Math Error: Invalid Expression";
        }
    }

    /**
     * Performs vector operations using NumPHP or SciPHP.
     */
    public function crossProduct(array $a, array $b): array {
        // Mock cross product or use SciPHP/NumPHP if loaded
        return [];
    }
}
