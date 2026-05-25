<?php
namespace Core\Virtual;

/**
 * HRITIK AI - LOGIC VISUALIZER
 * Generates an ASCII flow chart of the AI's reasoning process.
 */
class LogicVisualizer {
    
    public function generateFlow(array $steps): string {
        if (empty($steps)) return "[VISUAL] No logic steps recorded.";

        $output = "\n[NEURAL LOGIC FLOW]\n";
        $output .= "Input Prompt\n";
        
        foreach ($steps as $step) {
            $output .= "   │\n";
            $output .= "   ▼\n";
            $output .= "[ $step ]\n";
        }
        
        $output .= "   │\n";
        $output .= "   ▼\n";
        $output .= "Output Synthesis\n";

        return $output;
    }
}
