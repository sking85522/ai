<?php

namespace NumPHP\Types;

class Typename
{
    public static function typename($obj): string
    {
        return gettype($obj);
    }
}
