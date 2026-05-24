<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Loadtxt
{
    public static function loadtxt(string $fname, string $delimiter = ' '): NDArray
    {
        $handle = fopen($fname, 'r');
        if (!$handle) {
            throw new \Exception("Could not open file for reading: $fname");
        }

        $data = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $data[] = array_map('floatval', $row);
        }

        fclose($handle);

        return new NDArray($data);
    }
}