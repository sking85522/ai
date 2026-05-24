<?php

spl_autoload_register(function ($className) {
    if (strpos($className, 'PHPTorch\\') === 0) {
        $path = str_replace('PHPTorch\\', '', $className);
        $path = str_replace('\\', '/', $path);
        $file = __DIR__ . '/src/' . $path . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    } elseif ($className === 'torch') {
        require_once __DIR__ . '/PHPTorch.php';
    }
});
