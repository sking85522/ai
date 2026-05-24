<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Add
{
    /**
     * add
     *
     * @param mixed ...$args
     * @return mixed
     */
    public static function add(...$args)
    {
        if (count($args) !== 2) {
            throw new \InvalidArgumentException("add requires exactly two arguments.");
        }

        $a = $args[0];
        $b = $args[1];

        if (!$a instanceof NDArray || !$b instanceof NDArray) {
            throw new \InvalidArgumentException("Both arguments must be NDArray objects.");
        }

        $aData = $a->getData();
        $bData = $b->getData();

        $result = self::recursiveAdd($aData, $bData);

        return new NDArray($result);
    }

    private static function recursiveAdd($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            return array_map(function($aValue, $bValue) {
                return self::recursiveAdd($aValue, $bValue);
            }, $a, $b);
        } else {
            return (string) $a . (string) $b;
        }
    }
}
