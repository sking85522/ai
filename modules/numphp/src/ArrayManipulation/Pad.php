<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Pad
{
    /**
     * Pad an array.
     * Currently supports 1D and 2D arrays with 'constant' mode.
     *
     * @param NDArray $array
     * @param int|array $pad_width Number of values padded to the edges of each axis.
     * @param string $mode 'constant'
     * @param array $constant_values The values to set for the padded areas.
     * @return NDArray
     */
    public static function pad(NDArray $array, $pad_width, string $mode = 'constant', array $constant_values = [0]): NDArray
    {
        if ($mode !== 'constant') {
            throw new \InvalidArgumentException("Only 'constant' mode is supported for pad.");
        }

        $data = $array->getData();
        $shape = $array->getShape();
        $ndim = count($shape);

        if ($ndim === 1) {
            $pad_before = is_array($pad_width) ? $pad_width[0] : $pad_width;
            $pad_after = is_array($pad_width) ? $pad_width[1] : $pad_width;
            $val = $constant_values[0];

            $before = array_fill(0, $pad_before, $val);
            $after = array_fill(0, $pad_after, $val);

            return new NDArray(array_merge($before, $data, $after));
        }

        if ($ndim === 2) {
            $pad_rows = is_array($pad_width) ? $pad_width[0] : [$pad_width, $pad_width];
            $pad_cols = is_array($pad_width) ? $pad_width[1] : [$pad_width, $pad_width];
            $val = $constant_values[0];

            $new_cols = $shape[1] + $pad_cols[0] + $pad_cols[1];
            $padded_data = [];

            // Pad top
            for ($i = 0; $i < $pad_rows[0]; $i++) $padded_data[] = array_fill(0, $new_cols, $val);
            // Pad existing rows left/right
            foreach ($data as $row) {
                $padded_data[] = array_merge(array_fill(0, $pad_cols[0], $val), $row, array_fill(0, $pad_cols[1], $val));
            }
            // Pad bottom
            for ($i = 0; $i < $pad_rows[1]; $i++) $padded_data[] = array_fill(0, $new_cols, $val);
            return new NDArray($padded_data);
        }

        throw new \Exception("Pad currently only supports 1D and 2D arrays.");
    }
}