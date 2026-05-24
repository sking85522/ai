<?php

namespace NumPHP\Utils;

class Helpers
{
    public static function mapUnary($data, callable $fn)
    {
        if (is_array($data)) {
            $out = [];
            foreach ($data as $v) {
                $out[] = self::mapUnary($v, $fn);
            }
            return $out;
        }
        return $fn($data);
    }

    public static function mapBinary($a, $b, callable $fn)
    {
        if (is_array($a)) {
            $bArr = is_array($b) ? $b : array_fill(0, count($a), $b);
            $out = [];
            $n = count($a);
            for ($i = 0; $i < $n; $i++) {
                $out[] = self::mapBinary($a[$i], $bArr[$i], $fn);
            }
            return $out;
        }
        if (is_array($b)) {
            $out = [];
            $n = count($b);
            for ($i = 0; $i < $n; $i++) {
                $out[] = self::mapBinary($a, $b[$i], $fn);
            }
            return $out;
        }
        return $fn($a, $b);
    }

    public static function flatten($data, array &$out): void
    {
        if (is_array($data)) {
            foreach ($data as $v) {
                self::flatten($v, $out);
            }
            return;
        }
        $out[] = $data;
    }

    public static function buildFilled(array $shape, $value)
    {
        if (empty($shape)) {
            return $value;
        }
        $dim = array_shift($shape);
        $out = [];
        for ($i = 0; $i < $dim; $i++) {
            $out[] = self::buildFilled($shape, $value);
        }
        return $out;
    }
}
