<?php
namespace ParallelPHP\Core;

/**
 * MapReduce — Functional approach to parallel-style data processing.
 */
class MapReduce
{
    /**
     * Apply function to each element, processing in chunks.
     */
    public static function map(callable $func, array $data, int $chunkSize = 10): array
    {
        $results = [];
        $chunks = array_chunk($data, $chunkSize);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $results[] = $func($item);
            }
        }
        return $results;
    }

    /**
     * Map then Reduce.
     */
    public static function mapReduce(callable $mapFunc, callable $reduceFunc, array $data, $initial = null)
    {
        $mapped = array_map($mapFunc, $data);
        return array_reduce($mapped, $reduceFunc, $initial);
    }

    /**
     * Filter + Map combo.
     */
    public static function filterMap(callable $filterFunc, callable $mapFunc, array $data): array
    {
        $filtered = array_filter($data, $filterFunc);
        return array_map($mapFunc, $filtered);
    }
}
