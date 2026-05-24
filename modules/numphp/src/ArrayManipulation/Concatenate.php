<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Concatenate
{
    /**
     * Join a sequence of arrays along an existing axis.
     *
     * @param array $arrays Sequence of NDArrays
     * @param int $axis The axis along which the arrays will be joined.
     * @return NDArray
     */
    public static function concatenate(array $arrays, int $axis = 0): NDArray
    {
        if (empty($arrays)) {
            throw new \InvalidArgumentException("Need at least one array to concatenate");
        }

        /** @var NDArray $first */
        $first = $arrays[0];
        $baseShape = $first->getShape();
        $rank = count($baseShape);
        $dtype = $first->getDtype();

        // 1. Implementation for Axis 0 (Stacking rows/elements)
        if ($axis === 0) {
            $resultData = [];

            foreach ($arrays as $idx => $arr) {
                if (!$arr instanceof NDArray) {
                    throw new \InvalidArgumentException("All inputs must be NDArrays");
                }

                $shape = $arr->getShape();
                if (count($shape) !== $rank) {
                    throw new \InvalidArgumentException("All input arrays must have the same dimensionality");
                }

                // Check that dimensions other than axis 0 match
                for ($i = 1; $i < $rank; $i++) {
                    if ($shape[$i] !== $baseShape[$i]) {
                        throw new \InvalidArgumentException("All input arrays must have same shape, except for the concatenation axis.");
                    }
                }

                $data = $arr->getData();
                // Merge data
                if (!is_array($data)) {
                     // Scalar treated as 1-element array in context of 1D
                     $resultData[] = $data;
                } else {
                    $resultData = array_merge($resultData, $data);
                }
            }
            return new NDArray($resultData, $dtype);
        }

        // 2. Implementation for Axis 1 (Columns) - restricted to 2D for now
        if ($axis === 1 && $rank === 2) {
            $resultData = $first->getData();
            $rows = $baseShape[0];

            for ($k = 1; $k < count($arrays); $k++) {
                $nextArr = $arrays[$k];
                $nextData = $nextArr->getData();

                for ($i = 0; $i < $rows; $i++) {
                    $resultData[$i] = array_merge($resultData[$i], $nextData[$i]);
                }
            }
            return new NDArray($resultData, $dtype);
        }

        // 3. Implementation for Axis 2 - restricted to 3D for now
        if ($axis === 2 && $rank === 3) {
            $resultData = $first->getData();
            $rows = $baseShape[0];
            $cols = $baseShape[1];

            for ($k = 1; $k < count($arrays); $k++) {
                $nextArr = $arrays[$k];
                $nextData = $nextArr->getData();
                for ($i = 0; $i < $rows; $i++) {
                    for ($j = 0; $j < $cols; $j++) {
                        $resultData[$i][$j] = array_merge($resultData[$i][$j], $nextData[$i][$j]);
                    }
                }
            }
            return new NDArray($resultData, $dtype);
        }

        throw new \Exception("Concatenation currently only implemented for axis 0 (all ranks) and axis 1 (rank 2).");
    }
}
