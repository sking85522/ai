<?php

namespace ParallelPHP;

use ParallelPHP\Core\Pool;
use ParallelPHP\Core\TaskQueue;
use ParallelPHP\Core\MapReduce;

class ParallelPHP
{
    /**
     * Create a process pool for parallel execution.
     */
    public static function Pool(int $concurrency = 4): Pool
    {
        return new Pool($concurrency);
    }

    /**
     * Create a task queue for sequential/batched processing.
     */
    public static function TaskQueue(): TaskQueue
    {
        return new TaskQueue();
    }

    /**
     * Map a function across an array in parallel-like chunks.
     * @param callable $func Function to apply
     * @param array $data Input data
     * @param int $chunkSize Items per chunk
     * @return array Results
     */
    public static function map(callable $func, array $data, int $chunkSize = 10): array
    {
        return MapReduce::map($func, $data, $chunkSize);
    }

    /**
     * Map-Reduce: Map a function then reduce results.
     */
    public static function mapReduce(callable $mapFunc, callable $reduceFunc, array $data, $initial = null)
    {
        return MapReduce::mapReduce($mapFunc, $reduceFunc, $data, $initial);
    }
}
