<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Save
{
    public static function save(string $file, NDArray $arr): void
    {
        $data = ['shape' => $arr->getShape(), 'data' => $arr->getData(), 'dtype' => $arr->getDtype()];
        file_put_contents($file, json_encode($data));
    }
}