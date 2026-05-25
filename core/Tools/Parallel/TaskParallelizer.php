<?php
namespace Core\Tools\Parallel;

class TaskParallelizer {
    public function splitGoal(string $task): array {
        return array_values(array_filter(array_map('trim', preg_split('/\s+and\s+/i', $task) ?: [])));
    }

    public function parallelize(array $tasks): string {
        if (empty($tasks)) {
            return '[PARALLEL] No subtasks detected.';
        }

        $lines = ['[PARALLEL] Task queue prepared:'];
        foreach ($tasks as $idx => $task) {
            $lines[] = ($idx + 1) . '. ' . $task;
        }
        $lines[] = 'Execution strategy: run independent tasks separately and merge results.';
        return implode("\n", $lines);
    }
}
