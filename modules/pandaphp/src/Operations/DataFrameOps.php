<?php
namespace PandaPHP\Operations;

use PandaPHP\Core\DataFrame;

/**
 * DataFrame extended operations — groupby, merge, sort, apply, value_counts.
 */
class DataFrameOps
{
    /**
     * Group by a column and aggregate.
     * @param DataFrame $df
     * @param string $column Column to group by
     * @param array $aggs Aggregation rules: ['col' => 'sum|mean|count|min|max']
     * @return array Grouped results as associative array
     */
    public static function groupby(DataFrame $df, string $column, array $aggs): array
    {
        $columns = $df->columns();
        $shape = $df->shape();
        $groups = [];

        // Build groups
        for ($i = 0; $i < $shape[0]; $i++) {
            $key = $df->get($column, $i);
            if (!isset($groups[$key])) $groups[$key] = [];
            $row = [];
            foreach ($columns as $col) {
                $row[$col] = $df->get($col, $i);
            }
            $groups[$key][] = $row;
        }

        // Aggregate
        $result = [];
        foreach ($groups as $groupKey => $rows) {
            $aggResult = [$column => $groupKey];
            foreach ($aggs as $aggCol => $func) {
                $values = array_column($rows, $aggCol);
                $numValues = array_filter($values, 'is_numeric');
                switch ($func) {
                    case 'sum': $aggResult[$aggCol] = array_sum($numValues); break;
                    case 'mean': $aggResult[$aggCol] = count($numValues) > 0 ? array_sum($numValues) / count($numValues) : 0; break;
                    case 'count': $aggResult[$aggCol] = count($values); break;
                    case 'min': $aggResult[$aggCol] = !empty($numValues) ? min($numValues) : null; break;
                    case 'max': $aggResult[$aggCol] = !empty($numValues) ? max($numValues) : null; break;
                    default: $aggResult[$aggCol] = count($values); break;
                }
            }
            $result[] = $aggResult;
        }

        return $result;
    }

    /**
     * Merge two DataFrames on a common column (SQL-like JOIN).
     */
    public static function merge(DataFrame $left, DataFrame $right, string $on, string $how = 'inner'): array
    {
        $leftShape = $left->shape();
        $rightShape = $right->shape();
        $leftCols = $left->columns();
        $rightCols = $right->columns();

        // Build right lookup
        $rightLookup = [];
        for ($i = 0; $i < $rightShape[0]; $i++) {
            $key = $right->get($on, $i);
            $row = [];
            foreach ($rightCols as $col) {
                if ($col !== $on) $row[$col] = $right->get($col, $i);
            }
            $rightLookup[$key][] = $row;
        }

        $result = [];
        for ($i = 0; $i < $leftShape[0]; $i++) {
            $key = $left->get($on, $i);
            $leftRow = [];
            foreach ($leftCols as $col) {
                $leftRow[$col] = $left->get($col, $i);
            }

            if (isset($rightLookup[$key])) {
                foreach ($rightLookup[$key] as $rightRow) {
                    $result[] = array_merge($leftRow, $rightRow);
                }
            } elseif ($how === 'left') {
                $emptyRight = [];
                foreach ($rightCols as $col) {
                    if ($col !== $on) $emptyRight[$col] = null;
                }
                $result[] = array_merge($leftRow, $emptyRight);
            }
        }

        return $result;
    }

    /**
     * Sort DataFrame by column values.
     */
    public static function sortValues(DataFrame $df, string $column, bool $ascending = true): array
    {
        $shape = $df->shape();
        $columns = $df->columns();
        $rows = [];
        for ($i = 0; $i < $shape[0]; $i++) {
            $row = [];
            foreach ($columns as $col) {
                $row[$col] = $df->get($col, $i);
            }
            $rows[] = $row;
        }

        usort($rows, function($a, $b) use ($column, $ascending) {
            $cmp = $a[$column] <=> $b[$column];
            return $ascending ? $cmp : -$cmp;
        });

        return $rows;
    }

    /**
     * Count unique values in a column.
     */
    public static function valueCounts(DataFrame $df, string $column): array
    {
        $shape = $df->shape();
        $counts = [];
        for ($i = 0; $i < $shape[0]; $i++) {
            $val = $df->get($column, $i);
            $counts[$val] = ($counts[$val] ?? 0) + 1;
        }
        arsort($counts);
        return $counts;
    }

    /**
     * Apply a function to each value in a column.
     */
    public static function apply(DataFrame $df, string $column, callable $func): array
    {
        $shape = $df->shape();
        $result = [];
        for ($i = 0; $i < $shape[0]; $i++) {
            $result[] = $func($df->get($column, $i));
        }
        return $result;
    }

    /**
     * Statistical description of numeric columns.
     */
    public static function describe(DataFrame $df): array
    {
        $columns = $df->columns();
        $shape = $df->shape();
        $stats = [];

        foreach ($columns as $col) {
            $values = [];
            for ($i = 0; $i < $shape[0]; $i++) {
                $v = $df->get($col, $i);
                if (is_numeric($v)) $values[] = (float)$v;
            }
            if (empty($values)) continue;

            sort($values);
            $n = count($values);
            $sum = array_sum($values);
            $mean = $sum / $n;

            $variance = 0;
            foreach ($values as $v) $variance += ($v - $mean) ** 2;
            $std = sqrt($variance / max(1, $n - 1));

            $stats[$col] = [
                'count' => $n,
                'mean' => round($mean, 4),
                'std' => round($std, 4),
                'min' => $values[0],
                '25%' => $values[(int)floor($n * 0.25)],
                '50%' => $values[(int)floor($n * 0.5)],
                '75%' => $values[(int)floor($n * 0.75)],
                'max' => $values[$n - 1],
            ];
        }

        return $stats;
    }
}
