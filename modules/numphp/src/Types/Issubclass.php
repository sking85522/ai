<?php

namespace NumPHP\Types;

class Issubclass
{
    public static function issubclass_(string $class, string $parent): bool
    {
        return is_subclass_of($class, $parent);
    }
}
