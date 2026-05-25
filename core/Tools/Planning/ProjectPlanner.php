<?php
namespace Core\Tools\Planning;

class ProjectPlanner {
    public function plan(string $goal): string {
        $goal = trim($goal) ?: 'project';

        return "[PLAN] {$goal}\n" .
               "1. Define the expected input/output and success criteria.\n" .
               "2. Build the smallest working flow through API, engine, memory, NLP, and response.\n" .
               "3. Add tests for math, file tools, memory, and fallback behavior.\n" .
               "4. Harden unsafe commands and invalid paths.\n" .
               "5. Document how to run console.php and api.php.";
    }
}
