<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Split
{
    /**
     * Split an array into multiple sub-arrays.
     *
     * @param NDArray $ary
     * @param int|array $indices_or_sections If int, number of equal divisions. If array, sorted indices.
     * @param int $axis
     * @return NDArray[] Array of NDArrays
     */
    public static function split(NDArray $ary, $indices_or_sections, int $axis = 0): array
    {
        $data = $ary->getData();
        $shape = $ary->getShape();

        $rank = count($shape);
        if ($axis < 0 || $axis >= $rank) {
            throw new \InvalidArgumentException("axis out of bounds");
        }

        $total = $shape[$axis];
        $sections = [];
        $dtype = $ary->getDtype();

        if (is_int($indices_or_sections)) {
            $n = $indices_or_sections;
            if ($total % $n !== 0) {
                 throw new \InvalidArgumentException("array split does not result in an equal division");
            }
            $chunkSize = $total / $n;
            // array_chunk preserves keys by default false, which is what we want for re-indexing 0..N
            $chunks = self::splitData($data, $axis, $chunkSize);
            foreach ($chunks as $c) {
                $sections[] = new NDArray($c, $dtype);
            }
        } elseif (is_array($indices_or_sections)) {
            $indices = $indices_or_sections;
            $chunks = self::splitDataByIndices($data, $axis, $indices);
            foreach ($chunks as $c) {
                $sections[] = new NDArray($c, $dtype);
            }
        } else {
            throw new \InvalidArgumentException("indices_or_sections must be int or array of ints");
        }

        return $sections;
    }

    private static function splitData($data, int $axis, int $chunkSize): array
    {
        if ($axis === 0) {
            return array_chunk($data, $chunkSize);
        }
        $out = [];
        $numChunks = intdiv(count($data[0]), $chunkSize);
        for ($c = 0; $c < $numChunks; $c++) {
            $chunk = [];
            foreach ($data as $row) {
                $chunk[] = array_slice($row, $c * $chunkSize, $chunkSize);
            }
            $out[] = $chunk;
        }
        return $out;
    }

    private static function splitDataByIndices($data, int $axis, array $indices): array
    {
        $out = [];
        $last = 0;
        $indices[] = ($axis === 0) ? count($data) : count($data[0]);
        foreach ($indices as $idx) {
            if ($axis === 0) {
                $out[] = array_slice($data, $last, $idx - $last);
            } else {
                $chunk = [];
                foreach ($data as $row) {
                    $chunk[] = array_slice($row, $last, $idx - $last);
                }
                $out[] = $chunk;
            }
            $last = $idx;
        }
        return $out;
    }
}
