<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class _pyinstaller_hooks_dir
{
    public static function _pyinstaller_hooks_dir(...$args)
    {
        return \NumPHP\NumPHP::_pyinstaller_hooks_dir(...$args);
    }
}
