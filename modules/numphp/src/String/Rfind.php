<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Rfind
{
    /**
     * rfind
     *
     * @param mixed ...$args
     * @return mixed
     */
    public static function rfind(...$args)
    {
        if (count($args) < 2) {
            throw new \InvalidArgumentException("rfind expects at least 2 arguments");
        }
        $a = $args[0];
        $sub = $args[1];

        $aData = $a instanceof NDArray ? $a->getData() : $a;
        if (!is_array($aData)) {
            $aData = [$aData];
        }

        $result = self::recursiveRfind($aData, $sub);

        return new NDArray($result);
    }

    private static function recursiveRfind(array $data, string $sub): array
    {
        $result = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $result[] = self::recursiveRfind($item, $sub);
            } elseif (is_string($item)) {
                $result[] = strrpos($item, $sub);
            } else {
                $result[] = false;
            }
        }
        return $result;
    }
}
