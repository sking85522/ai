<?php
namespace ParallelPHP\Core;

/**
 * Task Queue — Manages and executes tasks sequentially or in batches.
 */
class TaskQueue
{
    private $tasks = [];
    private $results = [];

    public function addTask(callable $task, array $args = []): self
    {
        $this->tasks[] = ['callable' => $task, 'args' => $args];
        return $this;
    }

    public function run(): array
    {
        $this->results = [];
        foreach ($this->tasks as $idx => $task) {
            try {
                $this->results[$idx] = [
                    'status' => 'success',
                    'result' => call_user_func_array($task['callable'], $task['args']),
                ];
            } catch (\Throwable $e) {
                $this->results[$idx] = [
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }
        $this->tasks = [];
        return $this->results;
    }

    public function getResults(): array { return $this->results; }
    public function count(): int { return count($this->tasks); }
    public function clear(): void { $this->tasks = []; $this->results = []; }
}
