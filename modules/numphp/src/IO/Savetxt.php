<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Savetxt
{
    public static function savetxt(string $fname, NDArray $X, string $delimiter = ' '): void
    {
        $data = $X->getData();
        $handle = fopen($fname, 'w');
        if (!$handle) {
            throw new \Exception("Could not open file for writing: $fname");
        }

        foreach ($data as $row) {
            if (!is_array($row)) {
                fputcsv($handle, [$row], $delimiter);
            } else {
                fputcsv($handle, $row, $delimiter);
            }
        }

        fclose($handle);
    }
}
