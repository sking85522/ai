<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Fromfunction
{
    /**
     * Construct an array by executing a function over each coordinate.
     *
     * @param callable $function
     * @param array $shape
     * @param string|null $dtype
     * @return NDArray
     */
    public static function fromfunction(callable $function, array $shape, string $dtype = null): NDArray
    {
        $data = self::build($function, $shape, []);
        return new NDArray($data, $dtype);
    }

    private static function build(callable $function, array $shape, array $index)
    {
        if (empty($shape)) {
            return $function(...$index);
        }

        $dim = array_shift($shape);
        $result = [];
        for ($i = 0; $i < $dim; $i++) {
            $nextIndex = $index;
            $nextIndex[] = $i;
            $result[] = self::build($function, $shape, $nextIndex);
        }

        return $result;
    }
}
