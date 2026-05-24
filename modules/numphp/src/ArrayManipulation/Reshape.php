<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Reshape
{
    public static function reshape(NDArray $a, array $newShape): NDArray
    {
        $newShape = self::normalizeShape($newShape);
        // First, flatten the data
        $flatNDArray = Flatten::flatten($a);
        $flatData = $flatNDArray->getData();

        $totalElements = count($flatData);
        $expectedElements = array_product($newShape);

        if ($totalElements !== $expectedElements) {
            throw new \InvalidArgumentException(
                "cannot reshape array of size $totalElements into shape " . json_encode($newShape)
            );
        }

        // Reconstruct the array
        $reshapedData = self::buildShape($flatData, $newShape);

        return new NDArray($reshapedData, $a->getDType());
    }

    private static function normalizeShape(array $shape): array
    {
        $out = [];
        foreach ($shape as $dim) {
            if ($dim instanceof NDArray) {
                $dim = $dim->getData();
            }
            if (is_array($dim)) {
                $dim = $dim[0] ?? 0;
            }
            $out[] = (int) $dim;
        }
        return $out;
    }

    private static function buildShape(array &$data, array $shape)
    {
        if (empty($shape)) {
            return array_shift($data);
        }

        $dimSize = array_shift($shape);

        // If it's the last dimension, slice chunks directly
        if (empty($shape)) {
            $chunk = array_splice($data, 0, $dimSize);
            return $chunk;
        }

        $result = [];
        for ($i = 0; $i < $dimSize; $i++) {
            $result[] = self::buildShape($data, $shape);
        }

        return $result;
    }
}
