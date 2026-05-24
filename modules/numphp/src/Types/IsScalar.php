<?php

namespace NumPHP\Types;

class IsScalar
{
    /**
     * Returns True if the type of num is a scalar type.
     *
     * @param mixed $num
     * @return bool
     */
    public static function isscalar($num): bool
    {
        return is_scalar($num);
    }
}