<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Load
{
    public static function load(string $file): NDArray
    {
        if (!file_exists($file)) {
            throw new \Exception("File not found: $file");
        }
        $json = file_get_contents($file);
        $data = json_decode($json, true);

        return new NDArray($data['data'], $data['dtype']);
    }
}