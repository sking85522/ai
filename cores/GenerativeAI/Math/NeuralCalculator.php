<?php
namespace Core\GenerativeAI\Math;

/**
 * HRITIK AI - NEURAL CALCULATOR
 * Detects and solves mathematical expressions within the generative flow.
 */
class NeuralCalculator {
    
    /**
     * Finds and solves math problems in a prompt.
     */
    public function solve(string $prompt): ?string {
        if (preg_match('/([0-9\+\-\*\/\(\)\. ]+)=?/', $prompt, $matches)) {
            $expr = trim($matches[1]);
            // Use eval safely or a custom parser (simplified here)
            try {
                $res = @eval("return $expr;");
                if ($res !== false) return "Iska calculation $res hai.";
            } catch (\Throwable $e) {}
        }
        return null;
    }
}
