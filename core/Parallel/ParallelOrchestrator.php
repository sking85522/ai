<?php
namespace Core\Parallel;

/**
 * HRITIK AI - ADVANCED PARALLEL ORCHESTRATOR
 * High-performance task distribution engine for background neural processing.
 */
class ParallelOrchestrator {
    
    private int $maxWorkers = 50;

    /**
     * Alias for dispatch() to maintain compatibility with the main Engine.
     */
    public function dispatchAsyncTask(string $script, array $params = []): string {
        return $this->dispatch($script, $params);
    }

    /**
     * Dispatches a background task using Windows-native asynchronous execution.
     */
    public function dispatch(string $script, array $params = []): string {
        $cmd = "start /B h:\\xampp\\php\\php.exe " . escapeshellarg($script);
        foreach ($params as $key => $val) {
            $cmd .= " --$key=" . escapeshellarg($val);
        }
        
        exec($cmd);
        return "[PARALLEL] Task dispatched: $script";
    }

    public function runParallelBatch(array $tasks): void {
        foreach ($tasks as $task) {
            $this->dispatch($task['script'], $task['params'] ?? []);
        }
    }
}
