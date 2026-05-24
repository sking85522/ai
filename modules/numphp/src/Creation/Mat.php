<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Mat
{
    public static function mat($data): NDArray
    {
        if (is_string($data)) {
            $rows = array_map('trim', explode(';', trim($data)));
            $out = [];
            foreach ($rows as $row) {
                if ($row === '') continue;
                $parts = preg_split('/\s+/', trim($row));
                $out[] = array_map('floatval', $parts);
            }
            return new NDArray($out);
        }
        return new NDArray($data);
    }
}
