<?php

namespace NumPHP\Types;

class ResultType
{
    public static function result_type(...$args): string
    {
        $hasFloat = false;
        foreach ($args as $a) {
            if (is_float($a)) {
                $hasFloat = true;
                break;
            }
        }
        return $hasFloat ? 'float' : 'int';
    }
}
